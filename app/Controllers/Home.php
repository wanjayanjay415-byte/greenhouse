<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $stockModel = new \App\Models\StockInventoryModel();
        
        // Kita juga bisa tarik data komoditas yang sedang ADA atau semua
        $stocks = $stockModel->getStockWithProducts();

        return view('customer/home', [
            'title' => 'FreshGrow - Home | GreenHouse',
            'stocks' => $stocks
        ]);
    }

    public function catalog(): string
    {
        $stockModel = new \App\Models\StockInventoryModel();
        $orderModel = new \App\Models\OrderModel();
        $products = $stockModel->getStockWithProducts();

        // Kapasitas harian kurir (AKS Revisi 2.0)
        $todayKg = $orderModel->getTodayTotalWeight();

        return view('customer/catalog', [
            'title'     => 'Katalog Lengkap | GreenHouse',
            'products'  => $products,
            'todayKg'   => $todayKg,
            'maxDailyKg'=> 30,
        ]);
    }

    public function checkout(): string
    {
        $orderModel = new \App\Models\OrderModel();
        $todayKg = $orderModel->getTodayTotalWeight();
        
        return view('customer/checkout', [
            'title'      => 'Penyelesaian Pesanan | GreenHouse',
            'todayKg'    => $todayKg,
            'maxDailyKg' => 30,
        ]);
    }

    public function checkoutProcess()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
        }

        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $productModel   = new \App\Models\ProductModel();
        $stockModel     = new \App\Models\StockInventoryModel();
        $historyModel   = new \App\Models\OrderStatusHistoryModel();

        // Ambil data dari FormData
        $itemsStr      = $this->request->getPost('items');
        $items         = json_decode($itemsStr, true) ?? [];
        $address       = $this->request->getPost('address') ?? '';
        $paymentMethod = 'cod';

        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Keranjang kosong.']);
        }

        // [AKS 2.0] Cek kapasitas harian kurir (maks 30 kg/hari)
        $todayKg = $orderModel->getTodayTotalWeight();
        $orderTotalKg = 0;
        foreach ($items as $item) {
            $orderTotalKg += (int)($item['qty'] ?? 0);
        }
        if (($todayKg + $orderTotalKg) > 30) {
            $sisa = max(0, 30 - $todayKg);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kapasitas pengiriman hari ini sudah penuh (maks 30 Kg). Sisa kuota: ' . $sisa . ' Kg. Silakan pesan untuk hari berikutnya.'
            ]);
        }

        $proofName = null;

        // Keamanan: Hitung total dari database (menghindari Price Spoofing)
        $totalAmount = 0;
        $validatedItems = [];
        foreach ($items as $item) {
            $product = $productModel->where('name', $item['name'])->first();
            if ($product) {
                $qty = (int)$item['qty'];
                
                if ($qty <= 0) {
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => 'Kuantitas produk tidak valid.'
                    ]);
                }
                
                // Validasi stok final dilakukan di dalam transaksi (row lock) di bawah.
                $price = (float)$product['price_per_kg']; // AMBIL DARI DATABASE
                $totalAmount += $price * $qty;
                $validatedItems[] = [
                    'product_id'   => $product['id'],
                    'product_name' => $product['name'],
                    'qty'          => $qty,
                    'subtotal'     => $price * $qty
                ];
            }
        }

        if (empty($validatedItems)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak ditemukan di database.']);
        }

        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // [KRITIS] Kunci baris stok (SELECT ... FOR UPDATE) lalu validasi & kurangi
            // secara atomik untuk mencegah race condition / oversell.
            foreach ($validatedItems as $vItem) {
                $row = $db->table('stock_inventories')
                          ->where('product_id', $vItem['product_id'])
                          ->getCompiledSelect();
                $stock = $db->query($row . ' FOR UPDATE')->getRowArray();

                if (!$stock || (float)$stock['total_weight_kg'] < $vItem['qty']) {
                    $available = $stock ? (float)$stock['total_weight_kg'] : 0;
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal: Stok ' . $vItem['product_name'] . ' tidak mencukupi. (Sisa: ' . $available . ' Kg)'
                    ]);
                }
            }

            // Simpan order utama — status awal: Menunggu Konfirmasi (AKS 2.0)
            // COD: payment langsung 'paid' (bayar saat terima barang)
            $orderModel->insert([
                'order_number'    => $orderNumber,
                'customer_id'     => session()->get('id'),
                'total_amount'    => $totalAmount,
                'delivery_address'=> $address,
                'logistic_status' => 'Menunggu Konfirmasi',
                'payment_status'  => ($paymentMethod === 'cod') ? 'paid' : 'pending',
                'payment_method'  => $paymentMethod,
                'payment_proof'   => $proofName,
            ]);

            $orderId = $orderModel->getInsertID();

            // Catat histori status awal
            $historyModel->logStatusChange($orderId, null, 'Menunggu Konfirmasi', session()->get('id'));

            // Simpan order items & kurangi stok (baris sudah terkunci di atas)
            foreach ($validatedItems as $vItem) {
                $orderItemModel->insert([
                    'order_id'   => $orderId,
                    'product_id' => $vItem['product_id'],
                    'qty'        => $vItem['qty'],
                    'subtotal'   => $vItem['subtotal'],
                ]);

                $stock = $stockModel->where('product_id', $vItem['product_id'])->first();
                $newWeight = max(0, $stock['total_weight_kg'] - $vItem['qty']);
                $stockModel->update($stock['id'], [
                    'total_weight_kg' => $newWeight,
                    'status'          => stock_status($newWeight),
                    'last_updated'    => date('Y-m-d H:i:s')
                ]);
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Checkout gagal: ' . $e->getMessage());
        }

        if (!isset($orderId) || $db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pesanan. Silakan coba lagi.'
            ]);
        }

        $message = 'Pesanan berhasil dibuat! Pembayaran COD — bayar saat barang diterima. Status pesanan menunggu konfirmasi admin.';

        return $this->response->setJSON([
            'success'      => true,
            'order_number' => $orderNumber,
            'message'      => $message
        ]);
    }

    public function checkOrderUpdates()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') != 'customer') {
            return $this->response->setJSON(['success' => false]);
        }

        $orderModel = new \App\Models\OrderModel();
        $orders = $orderModel->select('id, logistic_status, payment_status')
                             ->where('customer_id', session()->get('id'))
                             ->findAll();

        return $this->response->setJSON(['success' => true, 'data' => $orders]);
    }

    /**
     * API: Cek kapasitas harian (untuk frontend)
     */
    public function getDailyCapacity()
    {
        $orderModel = new \App\Models\OrderModel();
        $todayKg = $orderModel->getTodayTotalWeight();

        return $this->response->setJSON([
            'success'   => true,
            'today_kg'  => $todayKg,
            'max_kg'    => 30,
            'remaining' => max(0, 30 - $todayKg),
            'is_full'   => ($todayKg >= 30)
        ]);
    }

    public function status()
    {
        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $historyModel   = new \App\Models\OrderStatusHistoryModel();
        $orders = [];

        $start_date = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-1 month'));
        $end_date   = $this->request->getGet('end_date') ?: date('Y-m-d');

        if (session()->get('isLoggedIn') && session()->get('role') == 'customer') {
            $orders = $orderModel->select('orders.*, users.full_name as customer_name, couriers.name as courier_name, couriers.phone as courier_phone, couriers.region as courier_region')
                                 ->join('users', 'users.id = orders.customer_id')
                                 ->join('couriers', 'couriers.id = orders.courier_id', 'left')
                                 ->where('orders.customer_id', session()->get('id'))
                                 ->where('DATE(orders.created_at) >=', $start_date)
                                 ->where('DATE(orders.created_at) <=', $end_date)
                                 ->orderBy('orders.created_at', 'DESC')
                                 ->findAll();
            
            // Attach items + status history to each order
            foreach ($orders as &$order) {
                $order['items'] = $orderItemModel->select('order_items.*, products.name as product_name, products.price_per_kg')
                                                 ->join('products', 'products.id = order_items.product_id')
                                                 ->where('order_id', $order['id'])
                                                 ->findAll();
                $order['status_history'] = $historyModel->getHistoryByOrderId($order['id']);
            }
        }

        return view('customer/status', [
            'title'      => 'Lacak Pesanan | GreenHouse',
            'orders'     => $orders,
            'start_date' => $start_date,
            'end_date'   => $end_date
        ]);
    }

    /**
     * Pesan Ulang (AKS 2.0) — salin item dari order lama ke cart
     */
    public function reorder($orderId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan login.']);
        }

        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $stockModel     = new \App\Models\StockInventoryModel();

        $order = $orderModel->where('id', $orderId)
                            ->where('customer_id', session()->get('id'))
                            ->first();
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
        }

        $items = $orderItemModel->select('order_items.*, products.name as product_name, products.price_per_kg, products.image_path')
                                ->join('products', 'products.id = order_items.product_id')
                                ->where('order_id', $orderId)
                                ->findAll();

        $cartItems = [];
        foreach ($items as $item) {
            $stock = $stockModel->where('product_id', $item['product_id'])->first();
            $cartItems[] = [
                'name'     => $item['product_name'],
                'price'    => (float)$item['price_per_kg'],
                'qty'      => (int)$item['qty'],
                'maxStock' => $stock ? (float)$stock['total_weight_kg'] : 0,
            ];
        }

        return $this->response->setJSON(['success' => true, 'items' => $cartItems]);
    }

    /**
     * Pembatalan pesanan oleh customer.
     * Hanya boleh saat status masih "Menunggu Konfirmasi".
     * Stok dikembalikan dalam transaksi & perubahan status dicatat.
     */
    public function cancelOrder($orderId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
        }

        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $stockModel     = new \App\Models\StockInventoryModel();
        $historyModel   = new \App\Models\OrderStatusHistoryModel();

        // Pastikan pesanan milik user yang login
        $order = $orderModel->where('id', $orderId)
                            ->where('customer_id', session()->get('id'))
                            ->first();
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
        }

        // Hanya pesanan yang belum dikonfirmasi yang bisa dibatalkan customer
        if ($order['logistic_status'] !== 'Menunggu Konfirmasi') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak bisa dibatalkan karena sudah diproses. Hubungi admin untuk bantuan.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Kembalikan stok tiap item
        $orderItems = $orderItemModel->where('order_id', $orderId)->findAll();
        foreach ($orderItems as $item) {
            $stock = $stockModel->where('product_id', $item['product_id'])->first();
            if ($stock) {
                $newWeight = $stock['total_weight_kg'] + $item['qty'];
                $stockModel->update($stock['id'], [
                    'total_weight_kg' => $newWeight,
                    'status'          => stock_status($newWeight),
                    'last_updated'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $orderModel->update($orderId, [
            'logistic_status' => 'Dibatalkan',
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // Catat histori perubahan status (oleh customer sendiri)
        $historyModel->logStatusChange($orderId, $order['logistic_status'], 'Dibatalkan', session()->get('id'));

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan pesanan karena kesalahan sistem.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Pesanan berhasil dibatalkan & stok dikembalikan.']);
    }

    public function profile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('id'));

        return view('customer/profile', [
            'title' => 'Profil Saya | GreenHouse',
            'user' => $user
        ]);
    }

    public function updateProfile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth'));
        }

        $userModel = new \App\Models\UserModel();
        $id = session()->get('id');

        $email = $this->request->getPost('email');
        
        // Cek jika email sudah dipakai user lain
        $existing = $userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to(base_url('profile'))->with('error', 'Gagal: Email sudah terdaftar oleh pengguna lain.');
        }

        $dataUpdate = [
            'full_name' => $this->request->getPost('full_name'),
            'phone'     => $this->request->getPost('phone'),
            'address'   => $this->request->getPost('address'),
            'email'     => $email,
            'two_factor_enabled' => $this->request->getPost('two_factor_enabled') ? 1 : 0
        ];

        if (!empty($this->request->getPost('password'))) {
            $dataUpdate['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);

        // Perbarui sesi untuk hal-hal vital yang disimpan
        session()->set('full_name', $dataUpdate['full_name']);
        session()->set('email', $dataUpdate['email']);

        return redirect()->to(base_url('profile'))->with('success', 'Profil dan pengaturan berhasil diperbarui!');
    }
}
