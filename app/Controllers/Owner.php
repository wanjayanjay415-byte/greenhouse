<?php

namespace App\Controllers;

class Owner extends BaseController
{
    private function getCommonData()
    {
        return [
            'sidebarTitle' => 'GreenHouse',
            'sidebarSub'   => 'EKSEKUTIF OWNER',
            'userName'     => session()->get('full_name') ?? 'Pemilik Bisnis'
        ];
    }

    public function index()
    {
        $orderModel   = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();
        $userModel    = new \App\Models\UserModel();

        $allOrders = $orderModel->findAll();
        $totalPenjualan = 0;
        foreach ($allOrders as $o) {
            $totalPenjualan += (float)$o['total_amount'];
        }

        $totalTransaksi = count($allOrders);

        // Produk Terlaris
        $db = \Config\Database::connect();
        $builder = $db->table('order_items');
        $builder->select('products.name, SUM(order_items.qty) as total_qty, SUM(order_items.subtotal) as total_revenue');
        $builder->join('products', 'products.id = order_items.product_id');
        $builder->groupBy('order_items.product_id');
        $builder->orderBy('total_qty', 'DESC');
        $builder->limit(5);
        $topProducts = $builder->get()->getResultArray();

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

        $data = $this->getCommonData();
        $data['totalPenjualan'] = $totalPenjualan;
        $data['totalTransaksi'] = $totalTransaksi;
        $data['topProducts'] = $topProducts;
        $data['totalCustomers'] = $userModel->where('role', 'customer')->countAllResults(false);
        $data['chartDates'] = $dailyDates;
        $data['chartRevenue'] = array_values($dailyRevenue);
        $data['monthlyLabels'] = $monthlyLabels;
        $data['monthlyRevenue'] = $monthlyRevenue;

        return view('owner/dashboard', $data);
    }

    // ==========================================
    // REPORTS & EXPORTS
    // ==========================================
    public function reports()
    {
        $reportModel = new \App\Models\ReportModel();
        $orderModel = new \App\Models\OrderModel();

        // Get filter inputs
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $tipeData = $this->request->getGet('tipe_data') ?? 'laporan_manajer';

        $data = $this->getCommonData();
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['tipeData'] = $tipeData;

        if ($tipeData == 'laporan_manajer') {
            $builder = $reportModel->orderBy('created_at', 'DESC');
            if ($startDate && $endDate) {
                $builder->where("period_start >=", $startDate)
                        ->where("period_end <=", $endDate);
            }
            $data['laporanData'] = $builder->findAll();
        } elseif ($tipeData == 'transaksi') {
            $builder = $orderModel->select('orders.*, users.full_name as customer_name')
                                  ->join('users', 'users.id = orders.customer_id')
                                  ->orderBy('orders.created_at', 'DESC');
            if ($startDate && $endDate) {
                $builder->where('DATE(orders.created_at) >=', $startDate)
                        ->where('DATE(orders.created_at) <=', $endDate);
            }
            $data['transaksiData'] = $builder->findAll();
        }

        return view('owner/reports', $data);
    }

    public function exportExcel()
    {
        $orderModel = new \App\Models\OrderModel();
        
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $orderModel->select('orders.order_number, users.full_name as customer_name, orders.total_amount, orders.payment_status, orders.logistic_status, orders.created_at')
                              ->join('users', 'users.id = orders.customer_id')
                              ->orderBy('orders.created_at', 'DESC');
                              
        if ($startDate && $endDate) {
            $builder->where('DATE(orders.created_at) >=', $startDate)->where('DATE(orders.created_at) <=', $endDate);
        }
        $transaksi = $builder->findAll();

        $filename = "Laporan_Transaksi_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        echo '<table border="1">';
        echo '<tr>
                <th style="background-color:#0b2e21; color:#ffffff;">Nomor Order</th>
                <th style="background-color:#0b2e21; color:#ffffff;">Pelanggan</th>
                <th style="background-color:#0b2e21; color:#ffffff;">Total (Rp)</th>
                <th style="background-color:#0b2e21; color:#ffffff;">Status Bayar</th>
                <th style="background-color:#0b2e21; color:#ffffff;">Status Pengiriman</th>
                <th style="background-color:#0b2e21; color:#ffffff;">Tanggal</th>
              </tr>';
              
        foreach ($transaksi as $row) {
            echo '<tr>';
            echo '<td>' . esc($row['order_number']) . '</td>';
            echo '<td>' . esc($row['customer_name']) . '</td>';
            echo '<td>' . number_format($row['total_amount'], 0, ',', '.') . '</td>';
            echo '<td>' . esc(strtoupper($row['payment_status'])) . '</td>';
            echo '<td>' . esc(strtoupper($row['logistic_status'])) . '</td>';
            echo '<td>' . date('d M Y H:i', strtotime($row['created_at'])) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    // ==========================================
    // APPROVE / REJECT LAPORAN
    // ==========================================
    public function approveReport($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $reportModel->update($id, ['status' => 'approved']);
        return redirect()->to(base_url('owner/reports'))->with('success', 'Laporan berhasil disetujui.');
    }

    public function viewReport($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $report = $reportModel->find($id);
        
        if (!$report) {
            return redirect()->to(base_url('owner/reports'))->with('error', 'Laporan tidak ditemukan.');
        }
        
        $contentData = json_decode($report['content'], true);
        
        $viewData = $this->getCommonData();
        $viewData['report'] = $report;
        $viewData['data'] = $contentData;
        
        return view('owner/view_report', $viewData);
    }

    public function rejectReport($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $reportModel->update($id, ['status' => 'reviewed']);
        return redirect()->to(base_url('owner/reports'))->with('success', 'Laporan telah ditinjau dan ditolak.');
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
        echo '<tr><th colspan="6" style="background-color:#0b2e21; color:#ffffff; font-size:16px;">LAPORAN: ' . strtoupper($report['title']) . '</th></tr>';
        echo '<tr><td colspan="6">Periode: ' . date('d M Y', strtotime($report['period_start'])) . ' s/d ' . date('d M Y', strtotime($report['period_end'])) . '</td></tr>';
        
        if ($data && isset($data['detail_orders'])) {
            echo '<tr><td colspan="6">Total Penjualan: Rp ' . number_format($data['total_penjualan'], 0, ',', '.') . '</td></tr>';
            echo '<tr><td colspan="6">Total Transaksi: ' . $data['total_transaksi'] . '</td></tr>';
            echo '<tr><td colspan="6">Jumlah Pembeli: ' . $data['jumlah_pembeli'] . '</td></tr>';
            echo '<tr><td colspan="6"></td></tr>';
            
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
            echo '<tr><td colspan="6"><strong>Daftar Pembeli:</strong> ' . implode(', ', $data['daftar_pembeli'] ?? []) . '</td></tr>';
        } else {
            // Laporan lama tanpa JSON terstruktur
            echo '<tr><td colspan="6"></td></tr>';
            echo '<tr><td colspan="6" style="vertical-align:top;"><strong>Catatan/Isi Laporan:</strong><br>' . nl2br(esc($report['content'])) . '</td></tr>';
        }
        
        echo '</table>';
        exit;
    }
    // KELOLA USER (Customer & Manager)
    // ==========================================
    public function users()
    {
        $userModel = new \App\Models\UserModel();
        $data = $this->getCommonData();
        // Owner only sees managers and customers
        $data['users'] = $userModel->whereIn('role', ['customer', 'manager'])->findAll();
        return view('owner/users', $data);
    }

    public function createUser()
    {
        $userModel = new \App\Models\UserModel();
        $role = $this->request->getPost('role');
        if (!in_array($role, ['customer', 'manager'])) {
            return redirect()->back()->with('error', 'Role tidak diizinkan untuk dibuat.');
        }
        
        $email = $this->request->getPost('email');
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Gagal: Email sudah terdaftar.');
        }

        $userModel->insert([
            'full_name'     => $this->request->getPost('full_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $role,
            'status'        => 'active'
        ]);

        return redirect()->to(base_url('owner/users'))->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser()
    {
        $userModel = new \App\Models\UserModel();
        $id = $this->request->getPost('id');
        $role = $this->request->getPost('role');
        
        if (!in_array($role, ['customer', 'manager'])) {
            return redirect()->back()->with('error', 'Update Gagal: Invalid Role.');
        }
        
        $email = $this->request->getPost('email');
        $existing = $userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Gagal: Email sudah digunakan oleh pengguna lain.');
        }

        $dataUpdate = [
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'role'      => $role,
            'status'    => $this->request->getPost('status')
        ];

        if (!empty($this->request->getPost('password'))) {
            $dataUpdate['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);
        return redirect()->to(base_url('owner/users'))->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        $userModel = new \App\Models\UserModel();
        // Protect from deleting other owners
        $user = $userModel->find($id);
        if ($user && in_array($user['role'], ['customer', 'manager'])) {
            // Prevent deletion if user has transaction history
            $orderModel = new \App\Models\OrderModel();
            if ($orderModel->where('customer_id', $id)->first()) {
                return redirect()->back()->with('error', 'Gagal: User memiliki riwayat transaksi dan tidak dapat dihapus untuk menjaga integritas data.');
            }
            
            $userModel->delete($id);
            return redirect()->to(base_url('owner/users'))->with('success', 'User berhasil dihapus.');
        }
        return redirect()->to(base_url('owner/users'))->with('error', 'Tindakan tidak diizinkan.');
    }

    // ==========================================
    // MONITORING (Pasif)
    // ==========================================
    public function monitoring()
    {
        $stockModel = new \App\Models\StockInventoryModel();
        $orderModel = new \App\Models\OrderModel();
        
        $data = $this->getCommonData();
        $data['stocks'] = $stockModel->getStockWithProducts();
        $data['orders'] = $orderModel->select('orders.*, users.full_name as customer_name')
                                     ->join('users', 'users.id = orders.customer_id')
                                     ->orderBy('orders.created_at', 'DESC')
                                     ->findAll();
                                     
        return view('owner/monitoring', $data);
    }

    // ==========================================
    // SETTINGS & BACKUP
    // ==========================================
    public function settings()
    {
        $data = $this->getCommonData();
        return view('owner/settings', $data);
    }

    public function updateProfile()
    {
        $userModel = new \App\Models\UserModel();
        $id = session()->get('id');

        $dataUpdate = [
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
        ];

        if (!empty($this->request->getPost('password'))) {
            $dataUpdate['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);

        // Update session
        session()->set('full_name', $dataUpdate['full_name']);
        
        return redirect()->to(base_url('owner/settings'))->with('success', 'Profil Owner Berhasil Diperbarui!');
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
            header('Content-Disposition: attachment; filename="'.basename($exportPath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($exportPath));
            readfile($exportPath);
            unlink($exportPath); // Delete after download
            exit;
        } else {
            if (file_exists($exportPath)) {
                unlink($exportPath);
            }
            return redirect()->back()->with('error', 'Gagal membuat backup database. Pastikan mysqldump tersedia.');
        }
    }
}
