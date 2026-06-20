<?php

namespace App\Controllers;

class Manager extends BaseController
{
    /**
     * Generate notifikasi real-time dari data database
     */
    private function getNotifications(): array
    {
        $notifications = [];
        $stockModel = new \App\Models\StockInventoryModel();
        $orderModel = new \App\Models\OrderModel();

        // 1. Cek stok KOSONG / RENDAH
        $stocks = $stockModel->getStockWithProducts();
        foreach ($stocks as $s) {
            if ($s['status'] == 'KOSONG') {
                $notifications[] = [
                    'icon'  => 'fa-solid fa-triangle-exclamation',
                    'color' => '#dc2626',
                    'bg'    => '#fef2f2',
                    'title' => 'Stok Habis: ' . $s['name'],
                    'msg'   => 'SKU ' . $s['sku'] . ' menunjukkan stok 0 Kg. Segera jadwalkan panen atau restock.',
                    'time'  => 'Perlu tindakan segera',
                    'type'  => 'danger'
                ];
            } elseif ($s['status'] == 'RENDAH') {
                $notifications[] = [
                    'icon'  => 'fa-solid fa-arrow-down',
                    'color' => '#d97706',
                    'bg'    => '#fffbeb',
                    'title' => 'Stok Menipis: ' . $s['name'],
                    'msg'   => 'SKU ' . $s['sku'] . ' tersisa ' . number_format($s['total_weight_kg'], 1) . ' Kg. Pertimbangkan untuk restock.',
                    'time'  => 'Peringatan',
                    'type'  => 'warning'
                ];
            }
        }

        // 2. Pesanan masuk hari ini
        $todayOrders = $orderModel
            ->select('orders.*, users.full_name')
            ->join('users', 'users.id = orders.customer_id')
            ->where('DATE(orders.created_at)', date('Y-m-d'))
            ->orderBy('orders.created_at', 'DESC')
            ->findAll();

        if (count($todayOrders) > 0) {
            $notifications[] = [
                'icon'  => 'fa-solid fa-chart-line',
                'color' => '#0b2e21',
                'bg'    => '#e8f9ef',
                'title' => 'Ringkasan Hari Ini',
                'msg'   => 'Terdapat ' . count($todayOrders) . ' transaksi masuk hari ini dengan total pesanan aktif.',
                'time'  => date('d M Y'),
                'type'  => 'info'
            ];
        }

        foreach ($todayOrders as $o) {
            if ($o['logistic_status'] == 'Menunggu Konfirmasi') {
                $notifications[] = [
                    'icon'  => 'fa-solid fa-cart-plus',
                    'color' => '#2563eb',
                    'bg'    => '#eff6ff',
                    'title' => 'Pesanan Baru: #' . $o['order_number'],
                    'msg'   => $o['full_name'] . ' melakukan pemesanan senilai Rp ' . number_format($o['total_amount'], 0, ',', '.'),
                    'time'  => date('H:i', strtotime($o['created_at'])),
                    'type'  => 'order'
                ];
            } elseif ($o['logistic_status'] == 'Selesai') {
                $notifications[] = [
                    'icon'  => 'fa-solid fa-circle-check',
                    'color' => '#16a34a',
                    'bg'    => '#f0fdf4',
                    'title' => 'Transaksi Selesai: #' . $o['order_number'],
                    'msg'   => 'Pesanan ' . $o['full_name'] . ' sudah diterima. Transaksi berhasil.',
                    'time'  => date('H:i', strtotime($o['updated_at'] ?? $o['created_at'])),
                    'type'  => 'success'
                ];
            }
        }

        // 3. Pesanan yang sedang dikirim (tracking)
        $inTransit = $orderModel
            ->select('orders.*, users.full_name')
            ->join('users', 'users.id = orders.customer_id')
            ->where('orders.logistic_status', 'Dalam Pengiriman')
            ->orderBy('orders.updated_at', 'DESC')
            ->findAll(3); // Ambil 3 terbaru

        foreach ($inTransit as $t) {
            $notifications[] = [
                'icon'  => 'fa-solid fa-truck-fast',
                'color' => '#7c3aed',
                'bg'    => '#f5f3ff',
                'title' => 'Dalam Pengiriman: #' . $t['order_number'],
                'msg'   => 'Pesanan ' . $t['full_name'] . ' sedang dalam perjalanan ke alamat tujuan.',
                'time'  => 'Tracking aktif',
                'type'  => 'tracking'
            ];
        }

        // 4. Laporan yang direview/approved (Notifikasi untuk Manager)
        $reportModel = new \App\Models\ReportModel();
        $recentReports = $reportModel->whereIn('status', ['reviewed', 'approved'])
                                     ->orderBy('updated_at', 'DESC')
                                     ->findAll(3);
        
        foreach ($recentReports as $r) {
            $isApproved = $r['status'] == 'approved';
            $notifications[] = [
                'icon'  => $isApproved ? 'fa-solid fa-file-circle-check' : 'fa-solid fa-file-circle-xmark',
                'color' => $isApproved ? '#16a34a' : '#ea580c',
                'bg'    => $isApproved ? '#f0fdf4' : '#fff7ed',
                'title' => 'Laporan ' . ($isApproved ? 'Disetujui' : 'Direview'),
                'msg'   => 'Laporan "' . $r['title'] . '" telah ' . ($isApproved ? 'disetujui' : 'ditinjau/ditolak') . ' oleh Owner.',
                'time'  => date('d M Y', strtotime($r['updated_at'])),
                'type'  => $isApproved ? 'success' : 'warning'
            ];
        }

        return $notifications;
    }

    public function index(): string
    {
        $stockModel   = new \App\Models\StockInventoryModel();
        $orderModel   = new \App\Models\OrderModel();
        $productModel = new \App\Models\ProductModel();
        $userModel    = new \App\Models\UserModel();

        // Statistik stok
        $stocks = $stockModel->getStockWithProducts();
        $totalStokKg = 0;
        $gradeACount = 0;
        foreach ($stocks as $s) {
            $totalStokKg += (float)$s['total_weight_kg'];
            if ($s['grade'] == 'A') $gradeACount++;
        }
        $gradeAPct = (count($stocks) > 0) ? round(($gradeACount / count($stocks)) * 100, 1) : 0;

        // Statistik pesanan (AKS 2.0: 5 tahap)
        $totalOrders     = $orderModel->countAll();
        $countPending    = $orderModel->where('logistic_status', 'Menunggu Konfirmasi')->countAllResults(false);
        $countConfirmed  = $orderModel->where('logistic_status', 'Dikonfirmasi')->countAllResults(false);
        $countHarvesting = $orderModel->where('logistic_status', 'Sedang Dipanen')->countAllResults(false);
        $countDelivering = $orderModel->where('logistic_status', 'Dalam Pengiriman')->countAllResults(false);
        $countDone       = $orderModel->where('logistic_status', 'Selesai')->countAllResults(false);

        // Kapasitas harian kurir (AKS 2.0)
        $todayKg = $orderModel->getTodayTotalWeight();

        // Pesanan terbaru (5)
        $recentOrders = $orderModel
            ->select('orders.*, users.full_name')
            ->join('users', 'users.id = orders.customer_id')
            ->orderBy('orders.created_at', 'DESC')
            ->findAll(5);

        // Total revenue
        $allOrders = $orderModel->findAll();
        $totalRevenue = 0;
        foreach ($allOrders as $o) {
            $totalRevenue += (float)$o['total_amount'];
        }

        // Users
        $totalUsers = $userModel->countAll();
        $totalCustomers = $userModel->where('role', 'customer')->countAllResults(false);

        // Stok rendah/kosong
        $lowStockItems = array_filter($stocks, fn($s) => $s['status'] == 'KOSONG' || $s['status'] == 'RENDAH');

        // Data untuk Grafik (7 Hari Terakhir & 12 Bulan)
        $dailyDates = [];
        $dailyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailyDates[] = date('d M', strtotime($date));
            $dailyRevenue[$date] = 0;
        }
        
        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyRevenue = array_fill(0, 12, 0);

        foreach ($allOrders as $o) {
            $date = date('Y-m-d', strtotime($o['created_at']));
            if (isset($dailyRevenue[$date])) {
                $dailyRevenue[$date] += (float)$o['total_amount'];
            }
            $monthIndex = (int)date('n', strtotime($o['created_at'])) - 1;
            $monthlyRevenue[$monthIndex] += (float)$o['total_amount'];
        }

        return view('manager/dashboard', [
            'sidebarTitle'      => 'GreenHouse',
            'sidebarSub'        => 'GREENHOUSE DATA CENTER',
            'userName'          => session()->get('full_name') ?? 'Admin',
            'notifications'     => $this->getNotifications(),
            'totalStokKg'       => $totalStokKg,
            'totalKomoditas'    => count($stocks),
            'gradeAPct'         => $gradeAPct,
            'totalOrders'       => $totalOrders,
            'countPending'      => $countPending,
            'countConfirmed'    => $countConfirmed,
            'countHarvesting'   => $countHarvesting,
            'countDelivering'   => $countDelivering,
            'countDone'         => $countDone,
            'recentOrders'      => $recentOrders,
            'totalRevenue'      => $totalRevenue,
            'totalUsers'        => $totalUsers,
            'totalCustomers'    => $totalCustomers,
            'stocks'            => $stocks,
            'lowStockItems'     => $lowStockItems,
            'chartDates'        => $dailyDates,
            'chartRevenue'      => array_values($dailyRevenue),
            'monthlyLabels'     => $monthlyLabels,
            'monthlyRevenue'    => $monthlyRevenue,
            'todayKg'           => $todayKg,
            'maxDailyKg'        => 30,
        ]);
    }

    public function stockReport()
    {
        $stockModel = new \App\Models\StockInventoryModel();
        $productModel = new \App\Models\ProductModel();

        $stocks = $stockModel->getStockWithProducts();

        // Hitung statistik real
        $totalStokKg = 0;
        $topProduct = null;
        $topWeight = 0;
        foreach ($stocks as $s) {
            $totalStokKg += (float)$s['total_weight_kg'];
            if ((float)$s['total_weight_kg'] > $topWeight) {
                $topWeight = (float)$s['total_weight_kg'];
                $topProduct = $s['name'];
            }
        }

        // Hitung demand % berdasarkan stok tertinggi
        $demandPct = ($totalStokKg > 0 && $topWeight > 0) ? round(($topWeight / $totalStokKg) * 100) : 0;

        $data = [
            'stocks'        => $stocks,
            'products'      => $productModel->findAll(),
            'totalStokKg'   => $totalStokKg,
            'totalKomoditas' => count($stocks),
            'topProduct'    => $topProduct ?: '-',
            'demandPct'     => $demandPct,
            'notifications' => $this->getNotifications(),
        ];

        return view('manager/stock_report', $data);
    }

    public function addStock()
    {
        $stockModel = new \App\Models\StockInventoryModel();
        
        $stockId = $this->request->getPost('stock_id');
        $addedWeight = $this->request->getPost('added_weight');
        $estimatedHarvest = $this->request->getPost('estimated_harvest');

        $currentStock = $stockModel->find($stockId);
        if($currentStock) {
            $newWeight = $currentStock['total_weight_kg'] + (float)$addedWeight;
            if ($newWeight < 0) $newWeight = 0;

            // Validasi batas maksimal stok per produk (30 Kg)
            if ($newWeight > MAX_STOCK_PER_PRODUCT) {
                $sisaKuota = max(0, MAX_STOCK_PER_PRODUCT - (float)$currentStock['total_weight_kg']);
                return redirect()->to(base_url('manager/stock_report'))->with('error', 'Gagal: Stok maksimal per produk adalah ' . MAX_STOCK_PER_PRODUCT . ' Kg. Sisa kuota yang dapat ditambahkan: ' . number_format($sisaKuota, 1) . ' Kg.');
            }

            $dataUpdate = [
                'total_weight_kg' => $newWeight,
                'status' => stock_status($newWeight),
                'last_updated' => date('Y-m-d H:i:s')
            ];
            // AKS 2.0: Simpan estimasi panen jika stok = 0
            if ($newWeight == 0 && !empty($estimatedHarvest)) {
                $dataUpdate['estimated_harvest'] = $estimatedHarvest;
            } elseif ($newWeight > 0) {
                $dataUpdate['estimated_harvest'] = null; // Clear estimasi jika stok ada
            }
            $stockModel->update($stockId, $dataUpdate);
        }

        return redirect()->to(base_url('manager/stock_report'))->with('success', 'Stok berhasil diperbarui.');
    }

    public function createProduct()
    {
        $productModel = new \App\Models\ProductModel();
        $stockModel   = new \App\Models\StockInventoryModel();

        $sku = $this->request->getPost('sku');
        $existingProduct = $productModel->where('sku', $sku)->first();
        if ($existingProduct) {
            return redirect()->to(base_url('manager/stock_report'))->with('error', 'Gagal: SKU sudah digunakan oleh produk lain.');
        }

        // Tangkap Foto dengan Validasi
        $imgFile = $this->request->getFile('product_image');
        $imgName = '';
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $ext = strtolower($imgFile->getClientExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imgName = $imgFile->getRandomName();
                $imgFile->move(FCPATH . 'images', $imgName);
            } else {
                return redirect()->to(base_url('manager/stock_report'))->with('error', 'Gagal: Format gambar produk harus JPG/PNG/WEBP.');
            }
        }

        // 1. Simpan Data Produk Baru
        $productId = $productModel->insert([
            'name'         => $this->request->getPost('name'),
            'category'     => $this->request->getPost('category'),
            'sku'          => $this->request->getPost('sku'),
            'price_per_kg' => $this->request->getPost('price_per_kg'),
            'image_path'   => $imgName,
        ]);

        // 2. Inisiasi Stok 0 KG ke Tabel Inventaris
        if ($productId) {
            $stockModel->insert([
                'product_id'      => $productId,
                'total_weight_kg' => 0,
                'grade'           => 'B',
                'status'          => 'KOSONG',
                'last_updated'    => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('manager/stock_report'))->with('success', 'Komoditas Sayur Baru Berhasil Didaftarkan!');
    }

    public function deleteProduct($id)
    {
        $productModel = new \App\Models\ProductModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        
        // Prevent deletion if product has transaction history
        $hasHistory = $orderItemModel->where('product_id', $id)->first();
        if ($hasHistory) {
            return redirect()->to(base_url('manager/stock_report'))->with('error', 'Produk tidak dapat dihapus karena memiliki riwayat transaksi. Ini akan merusak data laporan keuangan.');
        }

        $productModel->delete($id);

        return redirect()->to(base_url('manager/stock_report'))->with('success', 'Sayuran telah dihapus dari sistem gudang.');
    }

    public function editProduct()
    {
        $productModel = new \App\Models\ProductModel();
        $id = $this->request->getPost('product_id');

        $sku = $this->request->getPost('sku');
        $existingProduct = $productModel->where('sku', $sku)->where('id !=', $id)->first();
        if ($existingProduct) {
            return redirect()->to(base_url('manager/stock_report'))->with('error', 'Gagal: SKU sudah digunakan oleh produk lain.');
        }

        $updateData = [
            'name'         => $this->request->getPost('name'),
            'category'     => $this->request->getPost('category'),
            'sku'          => $sku,
            'price_per_kg' => $this->request->getPost('price_per_kg'),
        ];

        // Jika ada foto baru diupload, ganti dengan validasi
        $imgFile = $this->request->getFile('product_image');
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $ext = strtolower($imgFile->getClientExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imgName = $imgFile->getRandomName();
                $imgFile->move(FCPATH . 'images', $imgName);
                $updateData['image_path'] = $imgName;
            } else {
                return redirect()->to(base_url('manager/stock_report'))->with('error', 'Gagal: Format gambar produk harus JPG/PNG/WEBP.');
            }
        }

        $productModel->update($id, $updateData);

        return redirect()->to(base_url('manager/stock_report'))->with('success', 'Data produk berhasil diperbarui!');
    }

    public function distribution()
    {
        $orderModel = new \App\Models\OrderModel();
        // Default: 1 bulan terakhir (AKS 2.0)
        $orders = $orderModel->getRecentOrdersWithUser(1);

        // Lampirkan detail item ke tiap pesanan (untuk modal Detail Pesanan di admin)
        $orderItemModel = new \App\Models\OrderItemModel();
        $courierModel   = new \App\Models\CourierModel();
        $activeCouriers = $courierModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel
                ->select('order_items.*, products.name as product_name, products.price_per_kg')
                ->join('products', 'products.id = order_items.product_id')
                ->where('order_id', $order['id'])
                ->findAll();

            // Saran kurir berdasarkan kecocokan wilayah dengan alamat pengiriman
            $suggested = $courierModel->suggestByAddress($order['delivery_address'] ?? '');
            $order['suggested_courier_ids'] = array_column($suggested, 'id');
        }
        unset($order);

        // Hitung jumlah per status (AKS 2.0: 5 tahap)
        $countPending    = $orderModel->where('logistic_status', 'Menunggu Konfirmasi')->countAllResults(false);
        $countConfirmed  = $orderModel->where('logistic_status', 'Dikonfirmasi')->countAllResults(false);
        $countHarvesting = $orderModel->where('logistic_status', 'Sedang Dipanen')->countAllResults(false);
        $countDelivering = $orderModel->where('logistic_status', 'Dalam Pengiriman')->countAllResults(false);
        $countDone       = $orderModel->where('logistic_status', 'Selesai')->countAllResults(false);
        $countCancelled  = $orderModel->where('logistic_status', 'Dibatalkan')->countAllResults(false);

        // Kapasitas harian
        $todayKg = $orderModel->getTodayTotalWeight();

        return view('manager/distribution', [
            'orders'           => $orders,
            'countPending'     => $countPending,
            'countConfirmed'   => $countConfirmed,
            'countHarvesting'  => $countHarvesting,
            'countDelivering'  => $countDelivering,
            'countDone'        => $countDone,
            'countCancelled'   => $countCancelled,
            'totalOrders'      => $countPending + $countConfirmed + $countHarvesting + $countDelivering + $countDone,
            'notifications'    => $this->getNotifications(),
            'todayKg'          => $todayKg,
            'maxDailyKg'       => 30,
            'activeCouriers'   => $activeCouriers,
        ]);
    }

    public function updateOrderStatus()
    {
        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $stockModel     = new \App\Models\StockInventoryModel();
        $historyModel   = new \App\Models\OrderStatusHistoryModel();

        $orderId   = $this->request->getPost('order_id');
        $newStatus = $this->request->getPost('new_status');

        // AKS 2.0: Tidak boleh membatalkan pesanan Selesai
        $order = $orderModel->find($orderId);
        if ($order) {
            $oldStatus = $order['logistic_status'];

            if ($oldStatus === 'Selesai' && $newStatus === 'Dibatalkan') {
                return redirect()->to(base_url('manager/distribution'))->with('error', 'Pesanan yang sudah Selesai tidak dapat dibatalkan.');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Restore stock if cancelled
            if ($newStatus == 'Dibatalkan' && $oldStatus != 'Dibatalkan') {
                $orderItems = $orderItemModel->where('order_id', $orderId)->findAll();
                foreach ($orderItems as $item) {
                    $stock = $stockModel->where('product_id', $item['product_id'])->first();
                    if ($stock) {
                        $newWeight = $stock['total_weight_kg'] + $item['qty'];
                        $stockModel->update($stock['id'], [
                            'total_weight_kg' => $newWeight,
                            'status'          => stock_status($newWeight),
                            'last_updated'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            // Deduct stock if un-cancelled
            else if ($oldStatus == 'Dibatalkan' && $newStatus != 'Dibatalkan') {
                $orderItems = $orderItemModel->where('order_id', $orderId)->findAll();
                foreach ($orderItems as $item) {
                    $stock = $stockModel->where('product_id', $item['product_id'])->first();
                    if ($stock) {
                        $newWeight = max(0, $stock['total_weight_kg'] - $item['qty']);
                        $stockModel->update($stock['id'], [
                            'total_weight_kg' => $newWeight,
                            'status'          => stock_status($newWeight),
                            'last_updated'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            $updateData = [
                'logistic_status' => $newStatus,
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            if ($newStatus === 'Selesai') {
                $updateData['payment_status'] = 'paid';
            }

            // Auto-generate resi saat masuk "Dalam Pengiriman" bila belum ada
            if ($newStatus === 'Dalam Pengiriman' && empty($order['tracking_number'])) {
                $updateData['tracking_number'] = $this->generateTrackingNumber((int) $orderId);
            }

            $orderModel->update($orderId, $updateData);

            // AKS 2.0: Catat histori perubahan status
            $historyModel->logStatusChange($orderId, $oldStatus, $newStatus, session()->get('id'));

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('manager/distribution'))->with('error', 'Gagal memperbarui status karena kesalahan sistem.');
            }
        }

        return redirect()->to(base_url('manager/distribution'))->with('success', 'Status pesanan #' . $orderId . ' berhasil diubah ke "' . $newStatus . '".');
    }

    public function verifyPayment()
    {
        $orderModel = new \App\Models\OrderModel();
        $orderId = $this->request->getPost('order_id');

        $order = $orderModel->find($orderId);
        if ($order) {
            $orderModel->update($orderId, [
                'payment_status' => 'paid',
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to(base_url('manager/distribution'))->with('success', 'Pembayaran untuk pesanan #' . $order['order_number'] . ' berhasil diverifikasi!');
        }

        return redirect()->to(base_url('manager/distribution'))->with('error', 'Gagal memverifikasi pembayaran.');
    }

    public function rejectPayment()
    {
        $orderModel = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $stockModel = new \App\Models\StockInventoryModel();

        $orderId = $this->request->getPost('order_id');
        $order = $orderModel->find($orderId);

        if ($order) {
            // Restore stok
            $items = $orderItemModel->where('order_id', $orderId)->findAll();
            foreach ($items as $item) {
                $stock = $stockModel->where('product_id', $item['product_id'])->first();
                if ($stock) {
                    $newWeight = $stock['total_weight_kg'] + $item['qty'];
                    $stockModel->update($stock['id'], [
                        'total_weight_kg' => $newWeight,
                        'status'          => stock_status($newWeight),
                        'last_updated'    => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // Ubah status
            $orderModel->update($orderId, [
                'payment_status'  => 'failed',
                'logistic_status' => 'Dibatalkan',
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to(base_url('manager/distribution'))->with('success', 'Pembayaran ditolak. Pesanan dibatalkan dan stok telah dikembalikan.');
        }

        return redirect()->to(base_url('manager/distribution'))->with('error', 'Gagal menolak pembayaran.');
    }

    public function users()
    {
        $userModel = new \App\Models\UserModel();
        $users     = $userModel
            ->where('role', 'customer')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Hitung statistik
        $totalUsers    = count($users);
        $countActive   = count(array_filter($users, fn($u) => $u['status'] == 'active'));
        $countSuspend  = count(array_filter($users, fn($u) => $u['status'] == 'suspended'));

        return view('manager/users', [
            'users'         => $users,
            'totalUsers'    => $totalUsers,
            'countActive'   => $countActive,
            'countSuspend'  => $countSuspend,
            'notifications' => $this->getNotifications(),
        ]);
    }

    public function createUser()
    {
        $userModel = new \App\Models\UserModel();

        $fullName = trim((string)$this->request->getPost('full_name'));
        $email    = strtolower(trim((string)$this->request->getPost('email')));
        $phone    = trim((string)$this->request->getPost('phone'));
        $password = (string)$this->request->getPost('password');

        if ($fullName === '' || $email === '' || $password === '') {
            return redirect()->to(base_url('manager/users'))->with('error', 'Nama, email, dan password wajib diisi.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Format email tidak valid.');
        }

        if (strlen($password) < 6) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Password minimal 6 karakter.');
        }

        if ($userModel->where('email', $email)->first()) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal: Email sudah terdaftar.');
        }

        $created = $userModel->insert([
            'full_name'     => $fullName,
            'email'         => $email,
            'phone'         => $phone !== '' ? $phone : null,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'customer',
            'status'        => 'active',
        ]);

        if (!$created) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal menambahkan customer. Periksa kembali data yang dimasukkan.');
        }

        return redirect()->to(base_url('manager/users'))->with('success', 'Customer baru berhasil ditambahkan.');
    }

    public function editUser()
    {
        $userModel = new \App\Models\UserModel();
        $id = (int)$this->request->getPost('user_id');

        $user = $userModel->find($id);
        if (!$user || $user['role'] !== 'customer') {
            return redirect()->to(base_url('manager/users'))->with('error', 'Akses ditolak. Hanya akun customer yang dapat diubah dari menu ini.');
        }

        $fullName = trim((string)$this->request->getPost('full_name'));
        $email    = strtolower(trim((string)$this->request->getPost('email')));
        $phone    = trim((string)$this->request->getPost('phone'));
        $status   = (string)$this->request->getPost('status');

        if ($fullName === '' || $email === '') {
            return redirect()->to(base_url('manager/users'))->with('error', 'Nama dan email wajib diisi.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Format email tidak valid.');
        }

        if (!in_array($status, ['active', 'offline', 'suspended'], true)) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Status customer tidak valid.');
        }

        $updateData = [
            'full_name' => $fullName,
            'email'     => $email,
            'phone'     => $phone !== '' ? $phone : null,
            'role'      => 'customer',
            'status'    => $status,
        ];

        $existingEmail = $userModel->where('email', $updateData['email'])->where('id !=', $id)->first();
        if ($existingEmail) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal: Email sudah digunakan oleh pengguna lain.');
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            if (strlen((string)$password) < 6) {
                return redirect()->to(base_url('manager/users'))->with('error', 'Password baru minimal 6 karakter.');
            }

            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!$userModel->update($id, $updateData)) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal memperbarui data customer.');
        }

        return redirect()->to(base_url('manager/users'))->with('success', 'Data customer berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        $userModel = new \App\Models\UserModel();
        $id = (int)$id;

        $user = $userModel->find($id);

        if ($user && $id == session()->get('id')) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (!$user || $user['role'] !== 'customer') {
            return redirect()->to(base_url('manager/users'))->with('error', 'Akses ditolak. Hanya akun customer yang dapat dihapus dari menu ini.');
        }

        if (!$userModel->delete($id)) {
            return redirect()->to(base_url('manager/users'))->with('error', 'Gagal menghapus customer.');
        }

        return redirect()->to(base_url('manager/users'))->with('success', 'Akun customer berhasil dihapus dari sistem.');
    }

    // ==========================================
    // KELOLA KURIR / DRIVER (Revisi 4.0)
    // ==========================================
    public function couriers()
    {
        $courierModel = new \App\Models\CourierModel();
        $orderModel   = new \App\Models\OrderModel();

        $couriers = $courierModel->orderBy('created_at', 'DESC')->findAll();

        // Hitung jumlah pesanan aktif yang sedang ditangani tiap kurir
        foreach ($couriers as &$c) {
            $c['active_orders'] = $orderModel
                ->where('courier_id', $c['id'])
                ->whereIn('logistic_status', ['Dikonfirmasi', 'Sedang Dipanen', 'Dalam Pengiriman'])
                ->countAllResults();
        }
        unset($c);

        $totalCouriers = count($couriers);
        $countActive   = count(array_filter($couriers, fn($c) => $c['status'] === 'active'));

        return view('manager/couriers', [
            'couriers'      => $couriers,
            'totalCouriers' => $totalCouriers,
            'countActive'   => $countActive,
            'notifications' => $this->getNotifications(),
        ]);
    }

    public function createCourier()
    {
        $courierModel = new \App\Models\CourierModel();

        $name    = trim((string) $this->request->getPost('name'));
        $phone   = trim((string) $this->request->getPost('phone'));
        $address = trim((string) $this->request->getPost('address'));
        $region  = trim((string) $this->request->getPost('region'));
        $status  = (string) $this->request->getPost('status');

        if ($name === '' || $region === '') {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Nama dan wilayah kurir wajib diisi.');
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        $created = $courierModel->insert([
            'name'    => $name,
            'phone'   => $phone !== '' ? $phone : null,
            'address' => $address !== '' ? $address : null,
            'region'  => $region,
            'status'  => $status,
        ]);

        if (!$created) {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Gagal menambahkan kurir.');
        }

        return redirect()->to(base_url('manager/couriers'))->with('success', 'Kurir baru berhasil ditambahkan.');
    }

    public function editCourier()
    {
        $courierModel = new \App\Models\CourierModel();
        $id = (int) $this->request->getPost('courier_id');

        if (!$courierModel->find($id)) {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Kurir tidak ditemukan.');
        }

        $name    = trim((string) $this->request->getPost('name'));
        $phone   = trim((string) $this->request->getPost('phone'));
        $address = trim((string) $this->request->getPost('address'));
        $region  = trim((string) $this->request->getPost('region'));
        $status  = (string) $this->request->getPost('status');

        if ($name === '' || $region === '') {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Nama dan wilayah kurir wajib diisi.');
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        $courierModel->update($id, [
            'name'    => $name,
            'phone'   => $phone !== '' ? $phone : null,
            'address' => $address !== '' ? $address : null,
            'region'  => $region,
            'status'  => $status,
        ]);

        return redirect()->to(base_url('manager/couriers'))->with('success', 'Data kurir berhasil diperbarui.');
    }

    public function deleteCourier($id)
    {
        $courierModel = new \App\Models\CourierModel();
        $orderModel   = new \App\Models\OrderModel();
        $id = (int) $id;

        if (!$courierModel->find($id)) {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Kurir tidak ditemukan.');
        }

        // Lepaskan penugasan kurir dari pesanan yang masih aktif agar tidak menggantung
        $orderModel->where('courier_id', $id)
                   ->whereIn('logistic_status', ['Dikonfirmasi', 'Sedang Dipanen', 'Dalam Pengiriman'])
                   ->set(['courier_id' => null])
                   ->update();

        if (!$courierModel->delete($id)) {
            return redirect()->to(base_url('manager/couriers'))->with('error', 'Gagal menghapus kurir.');
        }

        return redirect()->to(base_url('manager/couriers'))->with('success', 'Kurir berhasil dihapus.');
    }

    /**
     * Tugaskan kurir ke pesanan. Bila pesanan sudah/akan dikirim & belum punya resi,
     * sistem otomatis membuat nomor resi.
     */
    public function assignCourier()
    {
        $orderModel   = new \App\Models\OrderModel();
        $courierModel = new \App\Models\CourierModel();

        $orderId   = (int) $this->request->getPost('order_id');
        $courierId = $this->request->getPost('courier_id');
        $courierId = ($courierId === '' || $courierId === null) ? null : (int) $courierId;

        $order = $orderModel->find($orderId);
        if (!$order) {
            return redirect()->to(base_url('manager/distribution'))->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($courierId !== null && !$courierModel->find($courierId)) {
            return redirect()->to(base_url('manager/distribution'))->with('error', 'Kurir tidak valid.');
        }

        $update = ['courier_id' => $courierId];

        // Auto-generate resi bila pesanan sedang dalam pengiriman & belum ada resi
        if ($courierId !== null && empty($order['tracking_number']) && $order['logistic_status'] === 'Dalam Pengiriman') {
            $update['tracking_number'] = $this->generateTrackingNumber($orderId);
        }

        $orderModel->update($orderId, $update);

        return redirect()->to(base_url('manager/distribution'))->with('success', 'Kurir berhasil ditugaskan untuk pesanan #' . esc($order['order_number']) . '.');
    }

    /**
     * Format resi: GH-YYYYMMDD-<id 4 digit>
     */
    private function generateTrackingNumber(int $orderId): string
    {
        return 'GH-' . date('Ymd') . '-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT);
    }

    public function reports()
    {
        $stockModel   = new \App\Models\StockInventoryModel();
        $orderModel   = new \App\Models\OrderModel();
        $reportModel  = new \App\Models\ReportModel();

        // Data otomatis (monitoring real-time)
        $stocks = $stockModel->getStockWithProducts();
        $totalStokKg = 0;
        foreach ($stocks as $s) {
            $totalStokKg += (float)$s['total_weight_kg'];
        }

        $totalOrders     = $orderModel->countAll();
        $countPending    = $orderModel->where('logistic_status', 'Menunggu Konfirmasi')->countAllResults(false);
        $countDelivering = $orderModel->where('logistic_status', 'Dalam Pengiriman')->countAllResults(false);
        $countDone       = $orderModel->where('logistic_status', 'Selesai')->countAllResults(false);

        $allOrders = $orderModel->findAll();
        $totalRevenue = 0;
        foreach ($allOrders as $o) {
            $totalRevenue += (float)$o['total_amount'];
        }

        $lowStockItems = array_filter($stocks, fn($s) => $s['status'] == 'KOSONG' || $s['status'] == 'RENDAH');

        // AKS 2.0: Hitung total kg terjual minggu ini (untuk target 80-90 kg/minggu)
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $db = \Config\Database::connect();
        $weeklyResult = $db->query("
            SELECT COALESCE(SUM(oi.qty), 0) as weekly_kg
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?
            AND o.logistic_status != 'Dibatalkan'
        ", [$weekStart, $weekEnd])->getRow();
        $weeklyKgSold = (float)($weeklyResult->weekly_kg ?? 0);

        // Laporan manual yang sudah dibuat
        $manualReports = $reportModel->orderBy('created_at', 'DESC')->findAll();

        return view('manager/reports', [
            'notifications'  => $this->getNotifications(),
            'totalStokKg'    => $totalStokKg,
            'totalKomoditas' => count($stocks),
            'stocks'         => $stocks,
            'totalOrders'    => $totalOrders,
            'countPending'   => $countPending,
            'countDelivering'=> $countDelivering,
            'countDone'      => $countDone,
            'totalRevenue'   => $totalRevenue,
            'lowStockItems'  => $lowStockItems,
            'manualReports'  => $manualReports,
            'weeklyKgSold'   => $weeklyKgSold,
            'weeklyTarget'   => 90,
            'weeklyTargetMin'=> 80,
        ]);
    }

    public function exportSalesExcel()
    {
        $orderModel = new \App\Models\OrderModel();
        
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan tentukan periode tanggal laporan.');
        }
        
        // Ambil order di rentang waktu (+ data kurir)
        $orders = $orderModel->select('orders.*, users.full_name as customer_name, users.email as customer_email, users.phone as customer_phone, couriers.name as courier_name')
                             ->join('users', 'users.id = orders.customer_id')
                             ->join('couriers', 'couriers.id = orders.courier_id', 'left')
                             ->where('DATE(orders.created_at) >=', $startDate)
                             ->where('DATE(orders.created_at) <=', $endDate)
                             ->orderBy('orders.created_at', 'DESC')
                             ->findAll();

        // Hitung Kg per order + rekap komoditas
        $orderItemModel = new \App\Models\OrderItemModel();
        $productStats   = [];
        $totalKg        = 0;
        foreach ($orders as &$o) {
            $items = $orderItemModel->select('order_items.*, products.name as product_name')
                                    ->join('products', 'products.id = order_items.product_id')
                                    ->where('order_id', $o['id'])
                                    ->findAll();
            $kg = 0;
            foreach ($items as $it) {
                $kg += (float)$it['qty'];
                if ($o['logistic_status'] != 'Dibatalkan') {
                    $pName = $it['product_name'] ?? 'Unknown';
                    $productStats[$pName] = ($productStats[$pName] ?? 0) + (float)$it['qty'];
                }
            }
            $o['total_kg'] = $kg;
            if ($o['logistic_status'] != 'Dibatalkan') $totalKg += $kg;
        }
        unset($o);
        arsort($productStats);

        $filename = 'Rekap_Penjualan_' . $startDate . '_sd_' . $endDate . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $thMain = 'background-color:#0b2e21; color:#ffffff; font-weight:bold; border:1px solid #0b2e21; padding:6px;';
        $thSub  = 'background-color:#e8ece2; color:#112a1f; font-weight:bold; border:1px solid #b9c2bb; padding:6px; text-align:center;';
        $td     = 'border:1px solid #d9d9d9; padding:5px;';

        echo '<meta charset="UTF-8">';
        echo '<table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; font-family:Calibri, Arial, sans-serif; font-size:11px;">';
        echo '<tr><th colspan="8" style="' . $thMain . ' font-size:16px; text-align:center;">REKAPITULASI PENJUALAN GREENHOUSE</th></tr>';
        echo '<tr><td colspan="8" style="text-align:center; ' . $td . '">Periode: ' . date('d M Y', strtotime($startDate)) . ' s/d ' . date('d M Y', strtotime($endDate)) . '</td></tr>';
        echo '<tr><td colspan="8" style="text-align:center; ' . $td . '">Dicetak: ' . date('d M Y H:i:s') . ' oleh ' . esc(session()->get('full_name') ?? 'Admin') . '</td></tr>';
        echo '<tr><td colspan="8"></td></tr>';

        // ====== Tabel Detail Transaksi ======
        echo '<tr>
                <th style="' . $thSub . '">No</th>
                <th style="' . $thSub . '">No. Order</th>
                <th style="' . $thSub . '">Nama Pembeli</th>
                <th style="' . $thSub . '">Total (Rp)</th>
                <th style="' . $thSub . '">Kg</th>
                <th style="' . $thSub . '">Pembayaran</th>
                <th style="' . $thSub . '">Status</th>
                <th style="' . $thSub . '">Kurir</th>
              </tr>';

        $totalPenjualan = 0;
        $totalTransaksi = count($orders);
        $no = 1;
        foreach ($orders as $row) {
            echo '<tr>';
            echo '<td style="' . $td . ' text-align:center;">' . $no++ . '</td>';
            echo '<td style="' . $td . '">' . esc($row['order_number']) . '</td>';
            echo '<td style="' . $td . '">' . esc($row['customer_name']) . '</td>';
            echo '<td style="' . $td . ' text-align:right;">' . number_format($row['total_amount'], 0, ',', '.') . '</td>';
            echo '<td style="' . $td . ' text-align:center;">' . $row['total_kg'] . '</td>';
            echo '<td style="' . $td . ' text-align:center;">' . esc(strtoupper($row['payment_method'])) . '</td>';
            echo '<td style="' . $td . '">' . esc($row['logistic_status']) . '</td>';
            echo '<td style="' . $td . '">' . esc($row['courier_name'] ?? '-') . '</td>';
            echo '</tr>';

            if ($row['logistic_status'] != 'Dibatalkan') {
                $totalPenjualan += (float)$row['total_amount'];
            }
        }

        echo '<tr>
                <td colspan="3" style="' . $td . ' font-weight:bold; text-align:right; background:#f0f4f2;">TOTAL (Non-Batal)</td>
                <td style="' . $td . ' font-weight:bold; text-align:right; background:#f0f4f2; color:#2e7d32;">Rp ' . number_format($totalPenjualan, 0, ',', '.') . '</td>
                <td style="' . $td . ' font-weight:bold; text-align:center; background:#f0f4f2;">' . number_format($totalKg, 1) . '</td>
                <td colspan="3" style="' . $td . ' background:#f0f4f2;">' . $totalTransaksi . ' transaksi</td>
              </tr>';
        echo '</table>';

        // ====== Rekap Komoditas ======
        echo '<br/><table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; font-family:Calibri, Arial, sans-serif; font-size:11px;">';
        echo '<tr><th colspan="3" style="' . $thMain . ' text-align:center;">REKAP KUANTITAS PER KOMODITAS</th></tr>';
        echo '<tr><th style="' . $thSub . '">No</th><th style="' . $thSub . '">Nama Komoditas</th><th style="' . $thSub . '">Total Kg Terjual</th></tr>';
        if (empty($productStats)) {
            echo '<tr><td colspan="3" style="' . $td . ' text-align:center;">Belum ada data penjualan komoditas.</td></tr>';
        } else {
            $n = 1;
            foreach ($productStats as $pName => $pKg) {
                echo '<tr>
                        <td style="' . $td . ' text-align:center;">' . $n++ . '</td>
                        <td style="' . $td . '">' . esc($pName) . '</td>
                        <td style="' . $td . ' text-align:right; font-weight:bold;">' . number_format($pKg, 1) . ' Kg</td>
                      </tr>';
            }
        }
        echo '</table>';
        exit;
    }

    /**
     * AKS 2.0: Export PDF Laporan Penjualan
     * Menggunakan HTML print-to-PDF yang optimal untuk browser
     */
    public function exportSalesPdf()
    {
        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan tentukan periode tanggal laporan.');
        }
        
        $orders = $orderModel->select('orders.*, users.full_name as customer_name')
                             ->join('users', 'users.id = orders.customer_id')
                             ->where('DATE(orders.created_at) >=', $startDate)
                             ->where('DATE(orders.created_at) <=', $endDate)
                             ->orderBy('orders.created_at', 'DESC')
                             ->findAll();
        
        $totalPenjualan = 0;
        $totalKg = 0;
        $pesananSukses = 0;
        $pesananBatal = 0;
        $productStats = [];
        
        foreach ($orders as &$o) {
            $items = $orderItemModel->select('order_items.*, products.name as product_name')
                                    ->join('products', 'products.id = order_items.product_id')
                                    ->where('order_id', $o['id'])
                                    ->findAll();
            $kg = 0;
            foreach ($items as $item) { 
                $kg += (float)$item['qty']; 
                if ($o['logistic_status'] != 'Dibatalkan') {
                    $pName = $item['product_name'] ?? 'Unknown';
                    if (!isset($productStats[$pName])) $productStats[$pName] = 0;
                    $productStats[$pName] += (float)$item['qty'];
                }
            }
            $o['total_kg'] = $kg;
            
            if ($o['logistic_status'] != 'Dibatalkan') {
                $totalPenjualan += (float)$o['total_amount'];
                $totalKg += $kg;
                $pesananSukses++;
            } else {
                $pesananBatal++;
            }
        }
        
        // Target mingguan
        $daysDiff = max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 + 1);
        $weeksInPeriod = max(1, ceil($daysDiff / 7));
        $kgPerWeek = round($totalKg / $weeksInPeriod, 1);
        $targetStatus = ($kgPerWeek >= 80) ? 'TERCAPAI' : 'BELUM TERCAPAI';
        $targetColor = ($kgPerWeek >= 80) ? '#2e7d32' : '#c62828';
        
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <title>Laporan Penjualan GreenHouse - ' . $startDate . ' s/d ' . $endDate . '</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #333; }
            h1 { font-size: 18px; color: #0b2e21; margin-bottom: 4px; }
            h2 { font-size: 14px; color: #333; margin: 20px 0 10px; border-bottom: 2px solid #0b2e21; padding-bottom: 5px; }
            .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #0b2e21; padding-bottom: 15px; }
            .subtitle { color: #666; font-size: 11px; }
            .stats-row { display: flex; gap: 15px; margin-bottom: 20px; }
            .stat-item { flex: 1; border: 1px solid #ddd; border-radius: 8px; padding: 12px; text-align: center; }
            .stat-val { font-size: 16px; font-weight: bold; color: #0b2e21; }
            .stat-lbl { font-size: 9px; color: #777; text-transform: uppercase; letter-spacing: 0.5px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #0b2e21; color: #fff; padding: 8px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
            td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 10px; }
            tr:nth-child(even) { background: #f9fafb; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .badge { padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
            .badge-green { background: #e8f5e9; color: #2e7d32; }
            .badge-red { background: #fce4ec; color: #c62828; }
            .target-box { border: 2px solid ' . $targetColor . '; border-radius: 8px; padding: 10px 15px; display: inline-block; margin-bottom: 15px; }
            .target-box strong { color: ' . $targetColor . '; }
            .footer { margin-top: 30px; text-align: center; color: #999; font-size: 9px; border-top: 1px solid #eee; padding-top: 10px; }
            .total-row td { font-weight: bold; border-top: 2px solid #0b2e21; background: #f0f4f2; }
            @page { size: A4 portrait; margin: 18mm 14mm; }
        </style>
        </head><body>
        <div class="header">
            <h1>LAPORAN PENJUALAN GREENHOUSE</h1>
            <div class="subtitle">Periode: ' . date('d M Y', strtotime($startDate)) . ' s/d ' . date('d M Y', strtotime($endDate)) . '</div>
            <div class="subtitle">Dicetak: ' . date('d M Y H:i:s') . '</div>
        </div>
        
        <div class="target-box">
            Target Mingguan (80-90 Kg): <strong>' . $kgPerWeek . ' Kg/minggu — ' . $targetStatus . '</strong>
        </div>
        
        <div class="stats-row">
            <div class="stat-item"><div class="stat-lbl">Total Pendapatan</div><div class="stat-val">Rp ' . number_format($totalPenjualan, 0, ',', '.') . '</div></div>
            <div class="stat-item"><div class="stat-lbl">Total Kg Terjual</div><div class="stat-val">' . number_format($totalKg, 1) . ' Kg</div></div>
            <div class="stat-item"><div class="stat-lbl">Pesanan Sukses</div><div class="stat-val">' . $pesananSukses . '</div></div>
            <div class="stat-item"><div class="stat-lbl">Pesanan Batal</div><div class="stat-val">' . $pesananBatal . '</div></div>
        </div>
        
        <h2>Kuantitas Terjual Per Sayuran (Kg)</h2>
        <table style="margin-bottom: 25px;">
            <thead><tr>
                <th style="width:70%;">Nama Komoditas</th>
                <th class="text-right">Total Kg Terjual</th>
            </tr></thead><tbody>';
        
        if (empty($productStats)) {
            $html .= '<tr><td colspan="2" class="text-center">Belum ada data penjualan komoditas.</td></tr>';
        } else {
            arsort($productStats);
            foreach ($productStats as $pName => $pKg) {
                $html .= '<tr>
                    <td>' . esc($pName) . '</td>
                    <td class="text-right" style="font-weight:bold;">' . number_format($pKg, 1) . ' Kg</td>
                </tr>';
            }
        }
        
        $html .= '</tbody></table>
        
        <h2>Detail Transaksi</h2>
        <table>
            <thead><tr>
                <th>No</th><th>No. Order</th><th>Nama Pembeli</th><th class="text-right">Total (Rp)</th>
                <th class="text-center">Kg</th><th class="text-center">Bayar</th><th>Status</th><th>Tanggal</th>
            </tr></thead><tbody>';
        
        $no = 1;
        foreach ($orders as $row) {
            $statusBadge = ($row['logistic_status'] == 'Selesai') ? 'badge-green' : (($row['logistic_status'] == 'Dibatalkan') ? 'badge-red' : '');
            
            $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . esc($row['order_number']) . '</td>
                <td>' . esc($row['customer_name']) . '</td>
                <td class="text-right">Rp ' . number_format($row['total_amount'], 0, ',', '.') . '</td>
                <td class="text-center">' . $row['total_kg'] . '</td>
                <td class="text-center">' . strtoupper($row['payment_method']) . '</td>
                <td><span class="badge ' . $statusBadge . '">' . $row['logistic_status'] . '</span></td>
                <td>' . date('d M Y H:i', strtotime($row['created_at'])) . '</td>
            </tr>';
        }
        
        $html .= '<tr class="total-row">
            <td colspan="3" class="text-right">TOTAL (Non-Batal):</td>
            <td class="text-right">Rp ' . number_format($totalPenjualan, 0, ',', '.') . '</td>
            <td class="text-center">' . number_format($totalKg, 1) . ' Kg</td>
            <td colspan="3"></td>
        </tr>';
        
        $html .= '</tbody></table>
        <div class="footer">
            &copy; ' . date('Y') . ' GreenHouse Management System — Laporan ini di-generate secara otomatis oleh sistem.<br>
            <strong>Dicetak oleh:</strong> ' . esc(session()->get('full_name') ?? 'Admin') . '
        </div>
        </body></html>';

        // Render ke PDF asli (A4) via Dompdf lalu paksa unduh
        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'Helvetica',
            'isHtml5ParserEnabled' => true,
        ]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_Penjualan_' . $startDate . '_sd_' . $endDate . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]); // Attachment=true => langsung terunduh
        exit;
    }

    public function previewReportData()
    {
        $orderModel     = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $stockModel     = new \App\Models\StockInventoryModel();
        
        $periodStart = $this->request->getPost('period_start');
        $periodEnd   = $this->request->getPost('period_end');
        
        if (!$periodStart || !$periodEnd) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih periode tanggal terlebih dahulu.']);
        }
        
        // Ambil order di rentang waktu
        $orders = $orderModel->select('orders.*, users.full_name as customer_name, users.email as customer_email, users.phone as customer_phone')
                             ->join('users', 'users.id = orders.customer_id')
                             ->where('DATE(orders.created_at) >=', $periodStart)
                             ->where('DATE(orders.created_at) <=', $periodEnd)
                             ->orderBy('orders.created_at', 'DESC')
                             ->findAll();
                             
        $totalPenjualan = 0;
        $pesananSukses = 0;
        $pesananBatal = 0;
        $pembeliUnik = [];
        $totalKg = 0;
        $detailOrders = [];
        $productStats = [];
        
        foreach ($orders as $o) {
            if ($o['logistic_status'] == 'Dibatalkan') {
                $pesananBatal++;
            } else {
                $pesananSukses++;
                $totalPenjualan += (float)$o['total_amount'];
            }
            
            // Hitung total Kg dari order items dan per komoditas
            $items = $orderItemModel->select('order_items.*, products.name as product_name')
                                    ->join('products', 'products.id = order_items.product_id')
                                    ->where('order_id', $o['id'])
                                    ->findAll();
            $orderKg = 0;
            foreach ($items as $item) {
                if ($o['logistic_status'] != 'Dibatalkan') {
                    $orderKg += (float)$item['qty'];
                    $pName = $item['product_name'] ?? 'Unknown';
                    if (!isset($productStats[$pName])) $productStats[$pName] = 0;
                    $productStats[$pName] += (float)$item['qty'];
                }
            }
            $totalKg += $orderKg;
            
            // Collect unique buyers
            $pembeliUnik[$o['customer_id']] = [
                'name' => $o['customer_name'],
                'email' => $o['customer_email'],
                'phone' => $o['customer_phone']
            ];
            
            $detailOrders[] = [
                'order_number'   => $o['order_number'],
                'customer_name'  => $o['customer_name'],
                'total_amount'   => $o['total_amount'],
                'payment_method' => $o['payment_method'],
                'logistic_status'=> $o['logistic_status'],
                'created_at'     => $o['created_at'],
                'total_kg'       => $orderKg
            ];
        }
        
        // Rata-rata per pesanan
        $avgPerOrder = ($pesananSukses > 0) ? ($totalPenjualan / $pesananSukses) : 0;
        
        // === AKS 2.0: Perbandingan dengan Periode Sebelumnya ===
        $daysDiff = (strtotime($periodEnd) - strtotime($periodStart)) / 86400 + 1;
        $prevEnd   = date('Y-m-d', strtotime($periodStart . ' -1 day'));
        $prevStart = date('Y-m-d', strtotime($prevEnd . " -" . ($daysDiff - 1) . " days"));
        
        $prevOrders = $orderModel->select('orders.total_amount, orders.logistic_status')
                                 ->where('DATE(orders.created_at) >=', $prevStart)
                                 ->where('DATE(orders.created_at) <=', $prevEnd)
                                 ->findAll();
        
        $prevPenjualan = 0;
        $prevSukses = 0;
        foreach ($prevOrders as $po) {
            if ($po['logistic_status'] != 'Dibatalkan') {
                $prevPenjualan += (float)$po['total_amount'];
                $prevSukses++;
            }
        }
        
        $revenueGrowth = ($prevPenjualan > 0) ? (($totalPenjualan - $prevPenjualan) / $prevPenjualan * 100) : 0;
        $orderGrowth   = ($prevSukses > 0)    ? (($pesananSukses - $prevSukses) / $prevSukses * 100) : 0;
        
        // === AKS 2.0: Target Mingguan 80-90 kg ===
        $weeksInPeriod = max(1, ceil($daysDiff / 7));
        $kgPerWeek = $totalKg / $weeksInPeriod;
        $targetStatus = ($kgPerWeek >= 80) ? 'Tercapai' : 'Belum Tercapai';
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_penjualan' => $totalPenjualan,
                'pesanan_sukses'  => $pesananSukses,
                'pesanan_batal'   => $pesananBatal,
                'pembeli'         => array_values($pembeliUnik),
                'total_kg'        => round($totalKg, 1),
                'avg_per_order'   => round($avgPerOrder),
                'kg_per_week'     => round($kgPerWeek, 1),
                'target_status'   => $targetStatus,
                'product_stats'   => $productStats,
                'revenue_growth'  => round($revenueGrowth, 1),
                'order_growth'    => round($orderGrowth, 1),
                'prev_period'     => $prevStart . ' s/d ' . $prevEnd,
                'prev_penjualan'  => $prevPenjualan,
                'detail_orders'   => $detailOrders,
                'stocks'          => $stockModel->getStockWithProducts()
            ]
        ]);
    }

    public function submitReport()
    {
        $reportModel = new \App\Models\ReportModel();
        $orderModel  = new \App\Models\OrderModel();

        $periodStart = $this->request->getPost('period_start');
        $periodEnd   = $this->request->getPost('period_end');

        // Ambil semua order dalam periode ini
        $orders = $orderModel->select('orders.*, users.full_name as customer_name')
                             ->join('users', 'users.id = orders.customer_id')
                             ->where('DATE(orders.created_at) >=', $periodStart)
                             ->where('DATE(orders.created_at) <=', $periodEnd)
                             ->orderBy('orders.created_at', 'DESC')
                             ->findAll();

        // Hitung ringkasan
        $totalPenjualan = 0;
        $totalTransaksi = count($orders);
        $daftarPembeli  = [];
        foreach ($orders as $o) {
            $totalPenjualan += (float)$o['total_amount'];
            if (!in_array($o['customer_name'], $daftarPembeli)) {
                $daftarPembeli[] = $o['customer_name'];
            }
        }

        // Simpan konten terstruktur sebagai JSON
        // Untuk Excel, kita butuh detail pesanan dan rekap komoditas
        $productStats = [];
        $orderItemModel = new \App\Models\OrderItemModel();
        foreach ($orders as $o) {
            $items = $orderItemModel->select('order_items.*, products.name as product_name')
                                    ->join('products', 'products.id = order_items.product_id')
                                    ->where('order_id', $o['id'])
                                    ->findAll();
            foreach ($items as $item) {
                if ($o['logistic_status'] != 'Dibatalkan') {
                    $pName = $item['product_name'] ?? 'Unknown';
                    if (!isset($productStats[$pName])) $productStats[$pName] = 0;
                    $productStats[$pName] += (float)$item['qty'];
                }
            }
        }
        arsort($productStats);

        $structuredContent = json_encode([
            'total_penjualan' => $totalPenjualan,
            'total_transaksi' => $totalTransaksi,
            'jumlah_pembeli'  => count($daftarPembeli),
            'daftar_pembeli'  => $daftarPembeli,
            'product_stats'   => $productStats,
            'catatan'         => $this->request->getPost('catatan'),
            'detail_orders'   => array_map(function($o) {
                return [
                    'order_number'   => $o['order_number'],
                    'customer_name'  => $o['customer_name'],
                    'total_amount'   => $o['total_amount'],
                    'payment_status' => $o['payment_status'],
                    'logistic_status'=> $o['logistic_status'],
                    'created_at'     => $o['created_at'],
                ];
            }, $orders),
        ], JSON_UNESCAPED_UNICODE);

        $reportModel->insert([
            'report_type'  => $this->request->getPost('report_type'),
            'title'        => $this->request->getPost('title'),
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'content'      => $structuredContent,
            'status'       => 'approved',
            'created_by'   => session()->get('id') ?? 1,
        ]);

        return redirect()->to(base_url('manager/reports'))->with('success', 'Laporan berhasil dibuat dan disimpan.');
    }

    public function deleteReport($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $report = $reportModel->find($id);

        if ($report) {
            $reportModel->delete($id);
            return redirect()->to(base_url('manager/reports'))->with('success', 'Laporan berhasil dihapus.');
        }

        return redirect()->to(base_url('manager/reports'))->with('error', 'Laporan tidak ditemukan.');
    }

    public function editReport()
    {
        $reportModel = new \App\Models\ReportModel();
        $id = $this->request->getPost('report_id');
        $report = $reportModel->find($id);

        if ($report) {
            $reportModel->update($id, [
                'title'       => $this->request->getPost('title'),
                'report_type' => $this->request->getPost('report_type')
            ]);
            return redirect()->to(base_url('manager/reports'))->with('success', 'Data laporan berhasil diperbarui.');
        }

        return redirect()->to(base_url('manager/reports'))->with('error', 'Laporan tidak ditemukan.');
    }

    public function exportReportExcel($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $report = $reportModel->find($id);
        
        if (!$report) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }
        
        $data = json_decode($report['content'], true);
        
        $filename = 'Laporan_' . preg_replace('/[^A-Za-z0-9]/', '_', $report['title']) . '_' . date('Ymd') . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        echo '<table border="1">';
        echo '<tr><th colspan="6" style="background-color:#0b2e21; color:#ffffff; font-size:16px;">LAPORAN: ' . esc(strtoupper($report['title'])) . '</th></tr>';
        echo '<tr><td colspan="6">Periode: ' . date('d M Y', strtotime($report['period_start'])) . ' s/d ' . date('d M Y', strtotime($report['period_end'])) . '</td></tr>';
        
        if ($data && isset($data['detail_orders'])) {
            echo '<tr><td colspan="6">Total Penjualan: Rp ' . number_format($data['total_penjualan'], 0, ',', '.') . '</td></tr>';
            echo '<tr><td colspan="6">Total Transaksi: ' . $data['total_transaksi'] . '</td></tr>';
            echo '<tr><td colspan="6">Jumlah Pembeli: ' . $data['jumlah_pembeli'] . '</td></tr>';
            echo '<tr><td colspan="6"></td></tr>';
            
            if (!empty($data['product_stats'])) {
                echo '<tr><th colspan="2" style="background-color:#e8ece2;">Nama Komoditas</th><th colspan="4" style="background-color:#e8ece2; text-align:left;">Total Kg Terjual</th></tr>';
                foreach ($data['product_stats'] as $pName => $pKg) {
                    echo '<tr><td colspan="2">' . esc($pName) . '</td><td colspan="4">' . number_format($pKg, 1) . ' Kg</td></tr>';
                }
                echo '<tr><td colspan="6"></td></tr>';
            }
            
            echo '<tr>
                    <th style="background-color:#e8ece2;">No Order</th>
                    <th style="background-color:#e8ece2;">Nama Pembeli</th>
                    <th style="background-color:#e8ece2;">Total Belanja (Rp)</th>
                    <th style="background-color:#e8ece2;">Status Bayar</th>
                    <th style="background-color:#e8ece2;">Status Logistik</th>
                    <th style="background-color:#e8ece2;">Tanggal</th>
                  </tr>';
            
            foreach ($data['detail_orders'] as $row) {
                echo '<tr>';
                echo '<td>' . esc($row['order_number']) . '</td>';
                echo '<td>' . esc($row['customer_name']) . '</td>';
                echo '<td>' . number_format($row['total_amount'], 0, ',', '.') . '</td>';
                echo '<td>' . esc(strtoupper($row['payment_status'])) . '</td>';
                echo '<td>' . esc(strtoupper($row['logistic_status'])) . '</td>';
                echo '<td>' . date('d M Y H:i', strtotime($row['created_at'])) . '</td>';
                echo '</tr>';
            }

            echo '<tr><td colspan="6"></td></tr>';
            echo '<tr><td colspan="6"><strong>Daftar Pembeli:</strong> ' . esc(implode(', ', $data['daftar_pembeli'] ?? [])) . '</td></tr>';
        } else {
            echo '<tr><td colspan="6"></td></tr>';
            echo '<tr><td colspan="6" style="vertical-align:top;"><strong>Catatan/Isi Laporan:</strong><br>' . nl2br(esc($report['content'])) . '</td></tr>';
        }
        
        echo '</table>';
        exit;
    }

    // ==========================================
    // LIHAT DETAIL LAPORAN (sebelumnya milik Owner)
    // ==========================================
    public function viewReport($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $report = $reportModel->find($id);

        if (!$report) {
            return redirect()->to(base_url('manager/reports'))->with('error', 'Laporan tidak ditemukan.');
        }

        $contentData = json_decode($report['content'], true);

        return view('manager/view_report', [
            'notifications' => $this->getNotifications(),
            'report'        => $report,
            'data'          => $contentData,
        ]);
    }

    // ==========================================
    // PENGATURAN & BACKUP (sebelumnya milik Owner)
    // ==========================================
    public function settings()
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('id'));

        return view('manager/settings', [
            'notifications' => $this->getNotifications(),
            'userName'      => session()->get('full_name') ?? 'Administrator',
            'user'          => $user,
        ]);
    }

    public function updateProfile()
    {
        $userModel = new \App\Models\UserModel();
        $id = session()->get('id');

        $email = $this->request->getPost('email');

        // Cek jika email sudah dipakai user lain
        $existing = $userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to(base_url('manager/settings'))->with('error', 'Gagal: Email sudah digunakan oleh pengguna lain.');
        }

        $dataUpdate = [
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $email,
            'phone'     => $this->request->getPost('phone'),
        ];

        if (!empty($this->request->getPost('password'))) {
            $dataUpdate['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);

        // Update session
        session()->set('full_name', $dataUpdate['full_name']);
        session()->set('email', $dataUpdate['email']);

        return redirect()->to(base_url('manager/settings'))->with('success', 'Profil Administrator Berhasil Diperbarui!');
    }

    public function backupDb()
    {
        $db = \Config\Database::connect();
        $database = $db->getDatabase();
        $username = $db->username;
        $password = $db->password;

        $backupFile = 'Backup_' . $database . '_' . date('Y-m-d-H-i-s') . '.sql';
        $exportPath = WRITEPATH . 'uploads/' . $backupFile;

        $escUser = escapeshellarg($username);
        $escDb   = escapeshellarg($database);
        $escPath = escapeshellarg($exportPath);

        // Tentukan lokasi binary mysqldump (XAMPP Linux fallback)
        $binary = 'mysqldump';
        foreach (['/opt/lampp/bin/mysqldump', '/usr/bin/mysqldump', '/usr/local/bin/mysqldump'] as $candidate) {
            if (file_exists($candidate)) {
                $binary = $candidate;
                break;
            }
        }

        // Password dikirim via env MYSQL_PWD agar tidak bocor di daftar proses
        // dan menghindari masalah escaping pada flag -p.
        $command = ($password ? 'MYSQL_PWD=' . escapeshellarg($password) . ' ' : '')
                 . escapeshellarg($binary) . " -u {$escUser} {$escDb} > {$escPath} 2>/dev/null";

        $returnVar = 0;
        system($command, $returnVar);

        if ($returnVar === 0 && file_exists($exportPath) && filesize($exportPath) > 0) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($exportPath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($exportPath));
            readfile($exportPath);
            unlink($exportPath); // Delete after download
            exit;
        } else {
            // Bersihkan file parsial jika dump gagal
            if (file_exists($exportPath)) {
                unlink($exportPath);
            }
            return redirect()->back()->with('error', 'Gagal membuat backup database. Pastikan mysqldump tersedia.');
        }
    }

    /**
     * API: Cek update pesanan untuk auto-sync admin distribution
     */
    public function checkOrderUpdatesAdmin()
    {
        $orderModel = new \App\Models\OrderModel();

        // Ambil jumlah pesanan per status dan timestamp terakhir
        $totalOrders     = $orderModel->countAll();
        $countPending    = $orderModel->where('logistic_status', 'Menunggu Konfirmasi')->countAllResults(false);
        $countConfirmed  = $orderModel->where('logistic_status', 'Dikonfirmasi')->countAllResults(false);
        $countHarvesting = $orderModel->where('logistic_status', 'Sedang Dipanen')->countAllResults(false);
        $countDelivering = $orderModel->where('logistic_status', 'Dalam Pengiriman')->countAllResults(false);
        $countDone       = $orderModel->where('logistic_status', 'Selesai')->countAllResults(false);

        // Timestamp update terakhir
        $latestOrder = $orderModel->orderBy('updated_at', 'DESC')->first();
        $latestTimestamp = $latestOrder ? $latestOrder['updated_at'] : null;

        return $this->response->setJSON([
            'success'          => true,
            'total_orders'     => $totalOrders,
            'count_pending'    => $countPending,
            'count_confirmed'  => $countConfirmed,
            'count_harvesting' => $countHarvesting,
            'count_delivering' => $countDelivering,
            'count_done'       => $countDone,
            'latest_timestamp' => $latestTimestamp,
        ]);
    }
}
