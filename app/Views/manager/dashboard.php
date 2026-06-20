<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item active"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .kpi-wrapper { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); height: 100%; display:flex; flex-direction:column; position:relative; overflow:hidden; transition: transform 0.2s;}
    .kpi-wrapper:hover { transform: translateY(-3px); }
    .kpi-title { font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; color: #555; margin-bottom: 24px; text-transform:uppercase;}
    .kpi-val { font-size: 2.8rem; font-weight: 800; color: #112a1f; line-height:1; letter-spacing:-1px;}
    .kpi-unit { font-size: 1rem; color: #888; font-weight: 600; margin-left:8px;}
    .kpi-stat-good { color: #2e7d32; font-weight: 700; font-size: 0.85rem; margin-top:auto; padding-top:20px;}
    .kpi-stat-warn { color: #d97706; font-weight: 700; font-size: 0.85rem; margin-top:auto; padding-top:20px;}
    .kpi-stat-neutral { color: #555; font-weight: 700; font-size: 0.85rem; margin-top:auto; padding-top:20px;}
    
    .bg-icon-leaf { position:absolute; bottom:-10px; right:10px; font-size:8rem; color:#f0f2f5; z-index:0; transform:rotate(-15deg); pointer-events:none;}
    .bg-icon-star { position:absolute; right:10px; top:30%; font-size:6rem; color:#f7f9fa; z-index:0; pointer-events:none;}
    .bg-icon-grid { position:absolute; bottom:10px; right:20px; font-size:5rem; color:#f0f2f5; z-index:0; pointer-events:none;}
    .bg-icon-truck { position:absolute; bottom:10px; right:10px; font-size:6rem; color:#f7f9fa; z-index:0; pointer-events:none;}
    .z-relative { position:relative; z-index:1; }

    .chart-box { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    
    .greenhouse-card { background: url('https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?auto=format&fit=crop&q=80') center/cover; border-radius: 20px; height: 100%; position: relative; overflow: hidden; padding: 40px; display: flex; flex-direction: column; justify-content: flex-end;}
    .greenhouse-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, rgba(11, 46, 33, 0.95) 0%, rgba(11, 46, 33, 0.4) 100%); }
    .gc-pill { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 20px; font-size: 0.7rem; font-weight:700; letter-spacing:1px; color:#fff; display:inline-block; margin-bottom: 20px; border:1px solid rgba(255,255,255,0.1); text-transform:uppercase;}

    .table-card { background:#fff; border-radius:20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow:hidden; }
    .table-logs th { font-size: 0.7rem; color: #888; font-weight: 700; letter-spacing: 1.5px; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;}
    .table-logs td { padding: 20px 24px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    
    .ava-circle { width:36px; height:36px; background:#e8ece2; color:#112a1f; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:800; font-size:0.8rem; margin-right:12px;}
    
    .status-mini { padding: 6px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display:inline-block; letter-spacing:0.5px;}
    .sm-masuk { background: #bcf0da; color: #0b2e21; }
    .sm-sortir { background: #eee8db; color: #5c5545; }
    .sm-kirim { background: #3b3127; color: #fff; }
    .sm-terima { background: #dcdcdc; color: #555; }

    .stock-alert-item { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f5f5f5; }
    .stock-alert-item:last-child { border-bottom:none; }
    .alert-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
</style>

<div class="mb-5">
    <h1 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif; font-size:2.8rem; letter-spacing:-1px;">Ringkasan Laporan Lapangan</h1>
    <p class="text-muted fs-6 mb-0">Data real-time dari database Greenhouse · <?= date('d F Y') ?></p>
</div>

<!-- KPI Cards Real -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="kpi-wrapper">
            <i class="fa-solid fa-leaf bg-icon-leaf"></i>
            <div class="kpi-title z-relative">TOTAL STOK GUDANG</div>
            <div class="d-flex align-items-baseline mb-4 z-relative">
                <span class="kpi-val"><?= number_format($totalStokKg / 1000, 1) ?></span><span class="kpi-unit">Ton</span>
            </div>
            <div class="kpi-stat-good z-relative"><i class="fa-solid fa-boxes-stacked me-1"></i> <?= $totalKomoditas ?> komoditas terdaftar</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-wrapper">
            <i class="fa-solid fa-star bg-icon-star"></i>
            <div class="kpi-title z-relative">KUALITAS GRADE A</div>
            <div class="d-flex align-items-baseline mb-4 z-relative">
                <span class="kpi-val"><?= $gradeAPct ?></span><span class="kpi-unit">%</span>
            </div>
            <div class="kpi-stat-<?= ($gradeAPct >= 70) ? 'good' : 'warn' ?> z-relative">
                <i class="fa-regular fa-circle-check me-1"></i> <?= ($gradeAPct >= 70) ? 'Sangat Baik' : 'Perlu Peningkatan' ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-wrapper">
            <i class="fa-solid fa-grip bg-icon-grid"></i>
            <div class="kpi-title z-relative">TOTAL PESANAN</div>
            <div class="d-flex align-items-baseline mb-4 z-relative">
                <span class="kpi-val"><?= $totalOrders ?></span><span class="kpi-unit">Order</span>
            </div>
            <div class="kpi-stat-neutral z-relative"><i class="fa-solid fa-truck-fast me-1"></i> <?= $countDelivering ?> sedang dikirim</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-wrapper">
            <i class="fa-solid fa-truck-fast bg-icon-truck"></i>
            <div class="kpi-title z-relative">TOTAL PENDAPATAN</div>
            <div class="d-flex align-items-baseline mb-4 z-relative">
                <span class="kpi-val" style="font-size:2.2rem;">Rp <?= number_format($totalRevenue / 1000000, 1, ',', '.') ?></span><span class="kpi-unit">Jt</span>
            </div>
            <div class="kpi-stat-good z-relative"><i class="fa-solid fa-wallet me-1"></i> <?= $countDone ?> transaksi selesai</div>
        </div>
    </div>
</div>

<!-- Chart + Greenhouse Status -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="chart-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0" style="color:#112a1f;">Status Pesanan Overview</h4>
                <span class="text-muted fw-bold" style="font-size:0.8rem;"><?= $totalOrders ?> total</span>
            </div>
            <div style="position:relative; height:280px; max-height:280px;">
                <canvas id="orderChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div style="background:#0b2e21; border-radius:20px; height:100%; padding:40px; display:flex; flex-direction:column; justify-content:flex-end; color:#fff;">
            <div style="background:rgba(255,255,255,0.1); padding:8px 16px; border-radius:20px; font-size:0.7rem; font-weight:700; letter-spacing:1px; display:inline-block; margin-bottom:20px; width:fit-content;">STATUS TERKINI</div>
            <h2 class="fw-bold mb-3" style="font-family:'Playfair Display', serif; line-height:1.2;">
                <?php if(count($lowStockItems) == 0): ?>
                    Semua Stok<br>Aman ✅
                <?php else: ?>
                    <?= count($lowStockItems) ?> Stok<br>Perlu Restock ⚠️
                <?php endif; ?>
            </h2>
            <p class="mb-3" style="font-size:0.9rem; opacity:0.8; line-height:1.6;">
                Total <?= $totalKomoditas ?> komoditas dalam gudang. <?= $totalUsers ?> pengguna terdaftar (<?= $totalCustomers ?> customer).
            </p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="<?= base_url('manager/stock_report') ?>" class="btn btn-sm text-white border border-white border-opacity-50 rounded-pill px-3" style="font-size:0.8rem;"><i class="fa-solid fa-seedling me-1"></i> Kelola Stok</a>
                <a href="<?= base_url('manager/distribution') ?>" class="btn btn-sm rounded-pill px-3" style="font-size:0.8rem; background:#bcf0da; color:#0b2e21;"><i class="fa-solid fa-truck me-1"></i> Pesanan</a>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Pendapatan Harian -->
<div class="row mb-5">
    <div class="col-12">
        <div class="chart-box">
            <h4 class="fw-bold mb-4" style="color:#112a1f;">Grafik Pendapatan (7 Hari Terakhir)</h4>
            <div style="position:relative; height:300px; max-height:300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Stok Alert + Recent Orders -->
<div class="row g-4 mb-5">
    <!-- Alert Stok -->
    <div class="col-lg-4">
        <div class="chart-box">
            <h5 class="fw-bold mb-4" style="color:#112a1f;"><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Alert Stok</h5>
            <?php if(!empty($lowStockItems)): ?>
                <?php foreach($lowStockItems as $ls): ?>
                <div class="stock-alert-item">
                    <div class="alert-dot" style="background:<?= ($ls['status'] == 'KOSONG') ? '#dc2626' : '#d97706' ?>;"></div>
                    <div style="flex:1;">
                        <div class="fw-bold text-dark" style="font-size:0.85rem;"><?= esc($ls['name']) ?></div>
                        <div class="text-muted" style="font-size:0.75rem;"><?= $ls['sku'] ?> · <?= number_format($ls['total_weight_kg'], 1) ?> Kg</div>
                    </div>
                    <span style="font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:12px; background:<?= ($ls['status'] == 'KOSONG') ? '#fef2f2' : '#fffbeb' ?>; color:<?= ($ls['status'] == 'KOSONG') ? '#dc2626' : '#d97706' ?>;"><?= $ls['status'] ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fa-solid fa-shield-check fa-2x mb-2 d-block" style="color:#2e7d32; opacity:0.5;"></i>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Semua stok dalam kondisi aman!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pesanan Terakhir -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                <h5 class="fw-bold m-0" style="color:#112a1f;">Pesanan Terbaru</h5>
                <a href="<?= base_url('manager/distribution') ?>" class="text-dark fw-bold text-decoration-none" style="font-size:0.8rem;">Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless table-logs align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ORDER</th>
                            <th>PELANGGAN</th>
                            <th>NOMINAL</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentOrders)): ?>
                            <?php foreach($recentOrders as $ro): ?>
                            <tr>
                                <td class="fw-bold text-dark" style="font-size:0.9rem;">#<?= esc($ro['order_number']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ava-circle"><?= strtoupper(substr($ro['full_name'], 0, 2)) ?></div>
                                        <span class="fw-bold text-dark" style="font-size:0.85rem;"><?= esc($ro['full_name']) ?></span>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark" style="font-size:0.85rem;">Rp <?= number_format($ro['total_amount'], 0, ',', '.') ?></td>
                                <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($ro['created_at'])) ?></td>
                                <td>
                                    <?php
                                        $dashStatusMap = [
                                            'Menunggu Konfirmasi' => ['class'=>'sm-masuk', 'label'=>'Menunggu'],
                                            'Dikonfirmasi'        => ['class'=>'sm-sortir', 'label'=>'Konfirm'],
                                            'Sedang Dipanen'      => ['class'=>'', 'style'=>'background:#fef9c3;color:#854d0e;', 'label'=>'Dipanen'],
                                            'Dalam Pengiriman'    => ['class'=>'sm-kirim', 'label'=>'Kirim'],
                                            'Selesai'             => ['class'=>'sm-terima', 'label'=>'Selesai'],
                                            'Dibatalkan'          => ['class'=>'', 'style'=>'background:#fee2e2;color:#dc2626;', 'label'=>'Batal'],
                                        ];
                                        $dSt = $dashStatusMap[$ro['logistic_status']] ?? ['class'=>'', 'label'=> $ro['logistic_status']];
                                    ?>
                                    <span class="status-mini <?= $dSt['class'] ?>" <?= isset($dSt['style']) ? 'style="'.$dSt['style'].'"' : '' ?>><?= $dSt['label'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data pesanan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Overview Table -->
<div class="table-card mb-5">
    <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-0">
        <h5 class="fw-bold m-0" style="color:#112a1f;">Inventaris Gudang Lengkap</h5>
        <a href="<?= base_url('manager/stock_report') ?>" class="text-dark fw-bold text-decoration-none" style="font-size:0.8rem;">Kelola Stok <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless table-logs align-middle mb-0">
            <thead>
                <tr>
                    <th>KOMODITAS</th>
                    <th>SKU</th>
                    <th>STOK (KG)</th>
                    <th>HARGA/KG</th>
                    <th>GRADE</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($stocks)): ?>
                    <?php foreach($stocks as $s): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($s['image_path']): ?>
                                    <img src="<?= base_url('images/'.$s['image_path']) ?>" style="width:36px; height:36px; border-radius:10px; object-fit:cover; margin-right:12px;" onerror="this.style.display='none'">
                                <?php endif; ?>
                                <span class="fw-bold text-dark" style="font-size:0.85rem;"><?= esc($s['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:0.8rem; letter-spacing:1px;"><?= $s['sku'] ?></td>
                        <td class="fw-bold text-dark font-monospace"><?= number_format($s['total_weight_kg'], 1) ?></td>
                        <td class="text-dark" style="font-size:0.85rem;">Rp <?= number_format($s['price_per_kg'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($s['grade'] == 'A'): ?>
                                <span class="fw-bold" style="color:#2e7d32; font-size:0.85rem;">● Grade A</span>
                            <?php else: ?>
                                <span class="fw-bold" style="color:#d97706; font-size:0.85rem;">● Grade B</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['status'] == 'ADA'): ?>
                                <span style="font-size:0.7rem; font-weight:700; padding:5px 12px; border-radius:12px; background:#bcf0da; color:#0b2e21;">Tersedia</span>
                            <?php elseif ($s['status'] == 'RENDAH'): ?>
                                <span style="font-size:0.7rem; font-weight:700; padding:5px 12px; border-radius:12px; background:#fef3c7; color:#d97706;">Rendah</span>
                            <?php else: ?>
                                <span style="font-size:0.7rem; font-weight:700; padding:5px 12px; border-radius:12px; background:#fee2e2; color:#dc2626;">KOSONG</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data inventaris.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Chart.js — Status Pesanan Donut
const ctx = document.getElementById('orderChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Sedang Dipanen', 'Dalam Pengiriman', 'Selesai'],
            datasets: [{
                data: [<?= $countPending ?>, <?= $countConfirmed ?>, <?= $countHarvesting ?>, <?= $countDelivering ?>, <?= $countDone ?>],
                backgroundColor: ['#bcf0da', '#eee8db', '#fef9c3', '#3b3127', '#dcdcdc'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, weight: '600', family: 'Inter' }
                    }
                },
                tooltip: {
                    backgroundColor: '#112a1f',
                    titleFont: { family: 'Inter', weight: '700' },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.label + ': ' + ctx.raw + ' pesanan';
                        }
                    }
                }
            }
        }
    });
}

// Line Chart — Pendapatan Harian
const revCtx = document.getElementById('revenueChart');
if (revCtx) {
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartDates ?? []) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($chartRevenue ?? []) ?>,
                borderColor: '#0b2e21',
                backgroundColor: 'rgba(11, 46, 33, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: '#f0f0f0' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
}
</script>
<?= $this->endSection() ?>
