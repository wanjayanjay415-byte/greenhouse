<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item active"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .report-card {
        background: #fff;
        border-radius: 24px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        border: 1px solid #f2f2f2;
    }
    
    .form-label-custom {
        font-size: 0.75rem;
        font-weight: 800;
        color: #555;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .form-control-custom {
        border-radius: 12px;
        padding: 14px;
        background: #f7f9fa;
        border: 1px solid #e1e5e8;
        font-weight: 600;
        color: #112a1f;
        transition: 0.2s;
    }
    .form-control-custom:focus {
        background: #fff;
        border-color: #0b2e21;
        box-shadow: 0 0 0 4px rgba(11, 46, 33, 0.1);
    }
    
    .btn-green-premium {
        background: #0b2e21;
        color: #fff;
        border: none;
        padding: 14px 28px;
        font-weight: 700;
        border-radius: 12px;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: 0.2s;
    }
    .btn-green-premium:hover {
        background: #0d3828;
        color: #fff;
        transform: translateY(-1px);
    }
    
    .btn-export {
        padding: 12px 22px;
        font-weight: 700;
        border-radius: 12px;
        font-size: 0.85rem;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid;
    }
    .btn-export:hover { transform: translateY(-1px); }
    .btn-excel { background: #fff; color: #198754; border-color: #198754; }
    .btn-excel:hover { background: #198754; color: #fff; }
    .btn-pdf { background: #fff; color: #dc3545; border-color: #dc3545; }
    .btn-pdf:hover { background: #dc3545; color: #fff; }

    .stat-box {
        padding: 28px;
        border-radius: 20px;
        border: 1px solid;
        position: relative;
        overflow: hidden;
    }
    .stat-box .stat-label {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #777;
    }
    .stat-box .stat-value {
        font-size: 1.7rem;
        font-weight: 800;
        margin-top: 8px;
    }
    .stat-box .stat-growth {
        font-size: 0.75rem;
        font-weight: 700;
        margin-top: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .growth-up { background: #e8f5e9; color: #2e7d32; }
    .growth-down { background: #fce4ec; color: #c62828; }
    .growth-flat { background: #f5f5f5; color: #666; }
    
    .target-badge {
        font-size: 0.8rem;
        font-weight: 800;
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.5px;
    }
    .target-achieved { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .target-missed { background: #fce4ec; color: #c62828; border: 1px solid #ef9a9a; }

    .table-rekap th {
        font-size: 0.7rem;
        color: #666;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 18px 20px;
        border-bottom: 2px solid #eef0f2;
        text-transform: uppercase;
    }
    .table-rekap td {
        padding: 16px 20px;
        border-bottom: 1px solid #f5f7f8;
        font-size: 0.85rem;
    }
    
    .status-badge {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-cod { background: #fff8e1; color: #b78103; }
    .badge-tf { background: #e3f2fd; color: #0d47a1; }
    
    .period-tab {
        background: #f0f2f5;
        border-radius: 12px;
        padding: 5px;
        display: inline-flex;
        gap: 4px;
    }
    .period-btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        color: #777;
        cursor: pointer;
        border: none;
        background: transparent;
        transition: 0.2s;
    }
    .period-btn.active {
        background: #fff;
        color: #112a1f;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    }
    .period-btn:hover:not(.active) { color: #333; }
    
    @media print {
        .no-print { display: none !important; }
        .report-card { box-shadow: none; border: 1px solid #ddd; }
    }
</style>

<div class="mb-5">
    <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">REPORTING CENTER</div>
    <h1 class="fw-bold mb-2" style="color:#112a1f; font-family:'Playfair Display', serif; font-size:2.8rem; letter-spacing:-1px;">Laporan Penjualan</h1>
    <p class="text-muted fs-6 mb-0">Analisis otomatis: total kg terjual, pendapatan, perbandingan periode, dan pencapaian target mingguan.</p>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success fw-bold border-0 shadow-sm mb-4" style="border-radius:12px;">
        <i class="fa-solid fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger fw-bold border-0 shadow-sm mb-4" style="border-radius:12px;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Shortcut Period Tabs + Filter -->
<div class="report-card mb-5 no-print">
    <h4 class="fw-bold mb-4" style="color:#112a1f;"><i class="fa-solid fa-filter me-2 text-success"></i> Filter Periode</h4>
    
    <div class="period-tab mb-4">
        <button class="period-btn" onclick="setPeriod('today')">Hari Ini</button>
        <button class="period-btn" onclick="setPeriod('week')">Minggu Ini</button>
        <button class="period-btn active" onclick="setPeriod('month')">Bulan Ini</button>
        <button class="period-btn" onclick="setPeriod('quarter')">3 Bulan</button>
    </div>
    
    <form id="filterForm" onsubmit="event.preventDefault(); loadRekapData();">
        <div class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Mulai</label>
                <input type="date" class="form-control form-control-custom" id="start_date" required value="<?= date('Y-m-01') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Akhir</label>
                <input type="date" class="form-control form-control-custom" id="end_date" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-green-premium flex-grow-1"><i class="fa-solid fa-arrows-rotate me-2"></i> Tarik Laporan Otomatis</button>
                    <button type="button" class="btn btn-export btn-excel" onclick="downloadExcel()"><i class="fa-solid fa-file-excel"></i> Excel</button>
                    <button type="button" class="btn btn-export btn-pdf" onclick="downloadPDF()"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ====== Laporan Tersimpan (Manual Reports) ====== -->
<div class="report-card mb-5 no-print">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold m-0" style="color:#112a1f;"><i class="fa-solid fa-folder-open me-2 text-success"></i> Laporan Tersimpan</h4>
        <button type="button" class="btn btn-green-premium" data-bs-toggle="modal" data-bs-target="#saveReportModal">
            <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Laporan Baru
        </button>
    </div>

    <?php if (empty($manualReports)): ?>
        <div class="text-center py-4 text-muted">
            <i class="fa-regular fa-folder-open fa-2x mb-2 d-block" style="opacity:0.3;"></i>
            Belum ada laporan tersimpan. Klik <strong>Simpan Laporan Baru</strong> untuk membuat snapshot penjualan suatu periode.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-borderless table-rekap align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Judul Laporan</th>
                        <th width="15%">Tipe</th>
                        <th width="25%">Periode</th>
                        <th width="12%">Dibuat</th>
                        <th width="13%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manualReports as $i => $rep): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></td>
                        <td class="fw-bold text-dark"><?= esc($rep['title']) ?></td>
                        <td><span class="status-badge badge-tf"><?= esc(strtoupper($rep['report_type'])) ?></span></td>
                        <td class="text-muted"><?= date('d M Y', strtotime($rep['period_start'])) ?> &ndash; <?= date('d M Y', strtotime($rep['period_end'])) ?></td>
                        <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($rep['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="<?= base_url('manager/view_report/' . $rep['id']) ?>" class="btn btn-sm btn-outline-dark" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                                <a href="<?= base_url('manager/export_report_excel/' . $rep['id']) ?>" class="btn btn-sm btn-outline-success" title="Export Excel"><i class="fa-solid fa-file-excel"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit"
                                    onclick="openEditReport(<?= $rep['id'] ?>, '<?= esc($rep['title'], 'js') ?>', '<?= esc($rep['report_type'], 'js') ?>')"><i class="fa-solid fa-pen"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                    onclick="confirmDeleteReport(<?= $rep['id'] ?>, '<?= esc($rep['title'], 'js') ?>')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Simpan Laporan Baru -->
<div class="modal fade" id="saveReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:18px; overflow:hidden;">
            <form action="<?= base_url('manager/submit_report') ?>" method="POST" data-turbo="false">
                <div class="modal-header text-white" style="background:#0b2e21; border:none;">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Laporan Penjualan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Sistem akan menarik & menyimpan rekap penjualan (transaksi, pembeli, komoditas) untuk periode yang dipilih sebagai snapshot permanen.</p>
                    <div class="mb-3">
                        <label class="form-label-custom">Judul Laporan</label>
                        <input type="text" name="title" class="form-control form-control-custom" required placeholder="Contoh: Laporan Penjualan Juni 2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Tipe Laporan</label>
                        <select name="report_type" class="form-control form-control-custom" required>
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan" selected>Bulanan</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label-custom">Periode Mulai</label>
                            <input type="date" name="period_start" class="form-control form-control-custom" required value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label-custom">Periode Akhir</label>
                            <input type="date" name="period_end" class="form-control form-control-custom" required value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control form-control-custom" rows="3" placeholder="Catatan manajer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-green-premium"><i class="fa-solid fa-check me-2"></i> Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Laporan -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:18px; overflow:hidden;">
            <form action="<?= base_url('manager/edit_report') ?>" method="POST" data-turbo="false">
                <div class="modal-header text-white" style="background:#0b2e21; border:none;">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2"></i> Edit Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="report_id" id="editReportId">
                    <div class="mb-3">
                        <label class="form-label-custom">Judul Laporan</label>
                        <input type="text" name="title" id="editReportTitle" class="form-control form-control-custom" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom">Tipe Laporan</label>
                        <select name="report_type" id="editReportType" class="form-control form-control-custom" required>
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-green-premium"><i class="fa-solid fa-check me-2"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hasil Rekap -->
<div id="rekapResultCard" style="display:none;">
    
    <!-- Target Badge -->
    <div class="report-card mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3" id="targetCard">
        <div>
            <div class="fw-bold text-dark mb-1" style="font-size:1.1rem;">Target Mingguan: 80 – 90 Kg/minggu</div>
            <div class="text-muted" style="font-size:0.85rem;">Rata-rata penjualan: <strong id="kgPerWeekVal">0</strong> Kg/minggu</div>
        </div>
        <span class="target-badge" id="targetBadge">-</span>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-box" style="background:#e8f5e9; border-color:#c8e6c9;">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value text-success" id="statRevenue">Rp 0</div>
                <span class="stat-growth" id="growthRevenue">-</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box" style="background:#e3f2fd; border-color:#bbdefb;">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value text-primary" id="statOrders">0</div>
                <span class="stat-growth" id="growthOrders">-</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box" style="background:#fce4ec; border-color:#ef9a9a;">
                <div class="stat-label">Total Kg Terjual</div>
                <div class="stat-value" style="color:#c62828;" id="statKg">0 Kg</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;" id="statAvgOrder">Rata-rata: Rp 0/pesanan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box" style="background:#fff8e1; border-color:#ffe082;">
                <div class="stat-label">Pembeli Unik</div>
                <div class="stat-value text-warning" id="statCustomers">0</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;" id="statBatal">0 pesanan dibatalkan</div>
            </div>
        </div>
    </div>
    
    <!-- Comparison Card -->
    <div class="report-card mb-4" id="comparisonCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0" style="color:#112a1f;"><i class="fa-solid fa-chart-line me-2 text-success"></i> Perbandingan Periode Sebelumnya</h5>
            <span class="text-muted fw-bold" style="font-size:0.8rem;" id="prevPeriodSpan">-</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#f7f9fa;">
                    <span class="text-muted fw-bold" style="font-size:0.85rem;">Pendapatan Periode Sebelumnya</span>
                    <span class="fw-bold text-dark" id="prevRevenueVal">Rp 0</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#f7f9fa;">
                    <span class="text-muted fw-bold" style="font-size:0.85rem;">Pertumbuhan Pendapatan</span>
                    <span class="fw-bold" id="revenueGrowthVal">0%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Table -->
    <div class="report-card" id="detailTableCard">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0" style="color:#112a1f;"><i class="fa-solid fa-list me-2 text-success"></i> Detail Transaksi</h4>
            <span class="text-muted fw-bold" id="periodSpan" style="font-size:0.8rem;">Periode: -</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-borderless table-rekap align-middle mb-0" id="rekapTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">No. Order</th>
                        <th width="20%">Nama Pembeli</th>
                        <th width="12%">Total (Rp)</th>
                        <th width="8%">Kg</th>
                        <th width="12%">Pembayaran</th>
                        <th width="12%">Status</th>
                        <th width="16%">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="rekapTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="report-card text-center py-5" id="emptyState">
    <i class="fa-solid fa-chart-bar fa-4x text-muted mb-3" style="opacity:0.3;"></i>
    <h5 class="text-muted fw-bold">Belum Ada Laporan yang Ditarik</h5>
    <p class="text-muted mb-0">Pilih periode tanggal di atas lalu tekan <strong>Tarik Laporan Otomatis</strong>.</p>
</div>

<script>
    // Period shortcut buttons
    function setPeriod(type) {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
        
        const today = new Date();
        let start, end = today.toISOString().split('T')[0];
        
        if (type === 'today') {
            start = end;
        } else if (type === 'week') {
            const dayOfWeek = today.getDay() || 7;
            const monday = new Date(today);
            monday.setDate(today.getDate() - dayOfWeek + 1);
            start = monday.toISOString().split('T')[0];
        } else if (type === 'month') {
            start = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-01';
        } else if (type === 'quarter') {
            const threeMonthsAgo = new Date(today);
            threeMonthsAgo.setMonth(today.getMonth() - 3);
            start = threeMonthsAgo.toISOString().split('T')[0];
        }
        
        document.getElementById('start_date').value = start;
        document.getElementById('end_date').value = end;
    }

    function loadRekapData() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        
        if(!start || !end) {
            Swal.fire('Perhatian', 'Harap isi kedua tanggal periode.', 'warning');
            return;
        }
        
        Swal.showLoading();
        
        fetch('<?= base_url('manager/preview_report_data') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `period_start=${start}&period_end=${end}`
        })
        .then(res => res.json())
        .then(res => {
            Swal.close();
            if(res.success) {
                const d = res.data;
                document.getElementById('emptyState').style.display = 'none';
                document.getElementById('rekapResultCard').style.display = 'block';
                
                // Stats
                document.getElementById('statRevenue').innerText = 'Rp ' + fmt(d.total_penjualan);
                document.getElementById('statOrders').innerText = d.pesanan_sukses + ' Pesanan';
                document.getElementById('statKg').innerText = d.total_kg + ' Kg';
                document.getElementById('statCustomers').innerText = d.pembeli.length + ' Orang';
                document.getElementById('statAvgOrder').innerText = 'Rata-rata: Rp ' + fmt(d.avg_per_order) + '/pesanan';
                document.getElementById('statBatal').innerText = d.pesanan_batal + ' pesanan dibatalkan';
                document.getElementById('periodSpan').innerText = `Periode: ${start} s/d ${end}`;
                
                // Target Badge
                document.getElementById('kgPerWeekVal').innerText = d.kg_per_week;
                const targetBadge = document.getElementById('targetBadge');
                if (d.target_status === 'Tercapai') {
                    targetBadge.className = 'target-badge target-achieved';
                    targetBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i> TARGET TERCAPAI';
                } else {
                    targetBadge.className = 'target-badge target-missed';
                    targetBadge.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> BELUM TERCAPAI';
                }
                
                // Growth
                setGrowth('growthRevenue', d.revenue_growth);
                setGrowth('growthOrders', d.order_growth);
                
                // Comparison
                document.getElementById('prevPeriodSpan').innerText = 'vs ' + d.prev_period;
                document.getElementById('prevRevenueVal').innerText = 'Rp ' + fmt(d.prev_penjualan);
                const rvg = document.getElementById('revenueGrowthVal');
                rvg.innerText = (d.revenue_growth >= 0 ? '+' : '') + d.revenue_growth + '%';
                rvg.style.color = d.revenue_growth >= 0 ? '#2e7d32' : '#c62828';
                
                // Render table
                const tbody = document.getElementById('rekapTableBody');
                tbody.innerHTML = '';
                
                if(d.detail_orders && d.detail_orders.length > 0) {
                    d.detail_orders.forEach((o, i) => {
                        const badgeClass = o.payment_method === 'cod' ? 'badge-cod' : 'badge-tf';
                        const badgeText = o.payment_method === 'cod' ? 'COD' : 'TRANSFER';
                        const statusColor = o.logistic_status === 'Dibatalkan' ? '#c62828' : (o.logistic_status === 'Selesai' ? '#2e7d32' : '#555');
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="text-muted fw-bold">${String(i + 1).padStart(2, '0')}</td>
                            <td class="fw-bold text-dark">#${o.order_number}</td>
                            <td>${o.customer_name}</td>
                            <td class="fw-bold text-success">Rp ${fmt(o.total_amount)}</td>
                            <td class="fw-bold">${o.total_kg} Kg</td>
                            <td><span class="status-badge ${badgeClass}">${badgeText}</span></td>
                            <td style="color:${statusColor}; font-weight:700; font-size:0.8rem;">${o.logistic_status}</td>
                            <td class="text-muted">${new Date(o.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit'})}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                    
                    // Initialize DataTables on rekap table
                    if ($.fn.DataTable.isDataTable('#rekapTable')) {
                        $('#rekapTable').DataTable().destroy();
                    }
                    $('#rekapTable').DataTable({
                        language: {
                            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_',
                            info: '_START_ - _END_ dari _TOTAL_', zeroRecords: 'Tidak ada data',
                            paginate: { next: '›', previous: '‹' }
                        },
                        pageLength: 15, order: [], dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi dalam periode ini.</td></tr>`;
                }
            } else {
                Swal.fire('Error', res.message || 'Gagal menarik data.', 'error');
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            console.error(err);
        });
    }
    
    function fmt(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }
    
    function setGrowth(id, val) {
        const el = document.getElementById(id);
        if (val > 0) {
            el.className = 'stat-growth growth-up';
            el.innerHTML = '<i class="fa-solid fa-arrow-up"></i> +' + val + '% vs sebelumnya';
        } else if (val < 0) {
            el.className = 'stat-growth growth-down';
            el.innerHTML = '<i class="fa-solid fa-arrow-down"></i> ' + val + '% vs sebelumnya';
        } else {
            el.className = 'stat-growth growth-flat';
            el.innerHTML = '<i class="fa-solid fa-minus"></i> 0% vs sebelumnya';
        }
    }
    
    function downloadExcel() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        if(!start || !end) { Swal.fire('Perhatian', 'Harap tentukan tanggal periode laporan.', 'warning'); return; }
        window.location.href = `<?= base_url('manager/export_sales_excel') ?>?start_date=${start}&end_date=${end}`;
    }
    
    function downloadPDF() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        if(!start || !end) { Swal.fire('Perhatian', 'Harap tentukan tanggal periode laporan.', 'warning'); return; }
        window.location.href = `<?= base_url('manager/export_sales_pdf') ?>?start_date=${start}&end_date=${end}`;
    }

    // ====== Laporan Tersimpan: Edit & Hapus ======
    function openEditReport(id, title, type) {
        document.getElementById('editReportId').value = id;
        document.getElementById('editReportTitle').value = title;
        document.getElementById('editReportType').value = type;
        new bootstrap.Modal(document.getElementById('editReportModal')).show();
    }

    function confirmDeleteReport(id, title) {
        Swal.fire({
            title: 'Hapus Laporan?',
            html: 'Laporan <strong>' + title + '</strong> akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url("manager/delete_report/") ?>' + id;
            }
        });
    }
</script>
<?= $this->endSection() ?>
