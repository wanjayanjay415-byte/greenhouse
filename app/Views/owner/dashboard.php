<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<a href="<?= base_url('owner/reports') ?>" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Laba/Rugi</a>
<a href="<?= base_url('owner/users') ?>" class="nav-item"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
<a href="<?= base_url('owner/monitoring') ?>" class="nav-item"><i class="fa-solid fa-layer-group"></i> Monitoring</a>
<a href="<?= base_url('owner/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .kpi-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); display: flex; flex-direction: column; position: relative; overflow: hidden; height: 100%; transition: transform 0.2s;}
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
    .kpi-title { font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; color: #555; margin-bottom: 24px; text-transform: uppercase; }
    .kpi-val { font-size: 2.5rem; font-weight: 800; color: #112a1f; line-height: 1; letter-spacing: -1px; }
    
    .bg-icon { position: absolute; bottom: 0; right: 10px; font-size: 6rem; color: #f4f6f8; z-index: 0; pointer-events: none; }
    .z-relative { position: relative; z-index: 1; }
    
    .chart-container { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); height: 100%; }
    
    .table-top { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; }
    .table-top th { font-size: 0.75rem; color: #888; font-weight: 700; letter-spacing: 1px; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase; }
    .table-top td { padding: 20px 24px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
</style>

<div class="mb-5">
    <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">EXECUTIVE OVERVIEW</div>
    <h1 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif; font-size:2.8rem; letter-spacing:-1px;">Pusat Monitoring Bisnis</h1>
    <p class="text-muted fs-6 mb-0">Ringkasan analitik dan laporan performa greenhouse secara keseluruhan.</p>
</div>

<!-- KPI ROW -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="kpi-card" style="background: #112a1f; color: #fff;">
            <i class="fa-solid fa-wallet bg-icon" style="color: rgba(255,255,255,0.05);"></i>
            <div class="kpi-title z-relative" style="color: #bcf0da;">TOTAL PENJUALAN</div>
            <div class="kpi-val z-relative text-white" style="font-size: 2rem;">Rp <?= number_format($totalPenjualan / 1000000, 1, ',', '.') ?> Juta</div>
            <div class="mt-auto pt-3 z-relative" style="font-size: 0.8rem; font-weight: 600; opacity: 0.8;">Bulan ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <i class="fa-solid fa-cart-shopping bg-icon"></i>
            <div class="kpi-title z-relative">TOTAL TRANSAKSI</div>
            <div class="kpi-val z-relative"><?= $totalTransaksi ?></div>
            <div class="mt-auto pt-3 z-relative text-success" style="font-size: 0.8rem; font-weight: 600;"><i class="fa-solid fa-arrow-trend-up me-1"></i> Stabil</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <i class="fa-solid fa-users bg-icon"></i>
            <div class="kpi-title z-relative">PELANGGAN AKTIF</div>
            <div class="kpi-val z-relative"><?= $totalCustomers ?></div>
            <div class="mt-auto pt-3 z-relative text-muted" style="font-size: 0.8rem; font-weight: 600;">Terdaftar di sistem</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <i class="fa-solid fa-star bg-icon"></i>
            <div class="kpi-title z-relative">PRODUK TERBAIK</div>
            <div class="kpi-val z-relative" style="font-size: 1.5rem; line-height: 1.2; padding-right:20px;">
                <?= !empty($topProducts) ? esc($topProducts[0]['name']) : '-' ?>
            </div>
            <div class="mt-auto pt-3 z-relative text-muted" style="font-size: 0.8rem; font-weight: 600;">
                (<?= !empty($topProducts) ? $topProducts[0]['total_qty'] : 0 ?> unit terjual)
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0" style="color:#112a1f;">Grafik Tren Pendapatan</h4>
                <select class="form-select form-select-sm" style="width: auto; border-radius:10px; font-weight:600; background:#f4f6f8; border:none; padding:8px 30px 8px 15px;">
                    <option>7 Hari Terakhir</option>
                    <option selected>30 Hari Terakhir</option>
                    <option>Tahun Ini</option>
                </select>
            </div>
            <div style="position:relative; height:300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-container">
            <h5 class="fw-bold mb-4" style="color:#112a1f;">Volume per Komoditas</h5>
            <div style="position:relative; height:250px;">
                <canvas id="topCommodityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TOP PRODUCTS TABLE -->
<div class="row mb-5">
    <div class="col-12">
        <div class="table-top">
            <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-3">
                <h5 class="fw-bold m-0" style="color:#112a1f;">Top 5 Produk Terlaris</h5>
                <button class="btn btn-sm" style="background:#f0f2f5; font-weight:600; border-radius:8px;">Lihat Laporan Penuh</button>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead style="background:#fafbfc;">
                        <tr>
                            <th>RANK</th>
                            <th width="40%">NAMA PRODUK</th>
                            <th>TOTAL TERJUAL</th>
                            <th>REVENUE GENERATED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($topProducts)): ?>
                            <?php foreach($topProducts as $index => $prod): ?>
                            <tr>
                                <td class="fw-bold px-4" style="color:#a0a5ad;">#<?= $index + 1 ?></td>
                                <td class="fw-bold text-dark" style="font-size:0.95rem;"><?= esc($prod['name']) ?></td>
                                <td class="fw-bold text-success font-monospace"><?= $prod['total_qty'] ?> KG</td>
                                <td class="fw-bold text-dark">Rp <?= number_format($prod['total_revenue'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi penjualan yang tercatat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dummy untuk grafik tren, karena grafik harian di MySQL membutuhkan query group by date
    // Untuk demo presentasi dashboard, kita bangun visualisasinya dengan Chart.js
    
    // Line Chart (Pendapatan Harian)
    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartDates) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($chartRevenue) ?>,
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

    // Doughnut Chart (Top Produk)
    const topCtx = document.getElementById('topCommodityChart');
    if (topCtx) {
        // Ambil data produk teratas dari PHP untuk divisualisasikan
        const rawNames = <?= json_encode(array_column($topProducts, 'name')) ?>;
        const rawQtys = <?= json_encode(array_column($topProducts, 'total_qty')) ?>;
        const fallbackNames = rawNames.length > 0 ? rawNames : ['Data Kosong'];
        const fallbackQtys = rawQtys.length > 0 ? rawQtys : [1];

        new Chart(topCtx, {
            type: 'doughnut',
            data: {
                labels: fallbackNames,
                datasets: [{
                    data: fallbackQtys,
                    backgroundColor: ['#0b2e21', '#2e7d32', '#7cb342', '#cddc39', '#e6ee9c'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15, font: {family: 'Inter', size:11} }
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
