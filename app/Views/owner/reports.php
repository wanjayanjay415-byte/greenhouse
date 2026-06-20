<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<a href="<?= base_url('owner/reports') ?>" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan</a>
<a href="<?= base_url('owner/users') ?>" class="nav-item"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
<a href="<?= base_url('owner/monitoring') ?>" class="nav-item"><i class="fa-solid fa-layer-group"></i> Monitoring</a>
<a href="<?= base_url('owner/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .report-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.02); padding:30px; margin-bottom:24px; border:1px solid #f0f0f0; transition:0.2s; }
    .report-card:hover { box-shadow:0 8px 30px rgba(0,0,0,0.05); }
    .kpi-mini { background:#f7f9fa; border-radius:12px; padding:20px; text-align:center; }
    .kpi-mini-val { font-size:1.8rem; font-weight:800; color:#112a1f; }
    .kpi-mini-label { font-size:0.7rem; font-weight:700; color:#888; letter-spacing:1px; text-transform:uppercase; margin-top:6px; }
</style>

<div class="mb-4">
    <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">BUSINESS INTELLIGENCE</div>
    <h2 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif;">Laporan Masuk dari Manager</h2>
    <p class="text-muted fs-6 mb-0">Review, setujui, dan unduh laporan penjualan yang dikirimkan oleh Manager.</p>
</div>

<!-- Filter -->
<div class="report-card mb-4" style="background:#f0f2f5; border:none;">
    <form action="<?= base_url('owner/reports') ?>" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" style="font-size:0.75rem; font-weight:700;">TIPE LAPORAN</label>
            <select name="tipe_data" class="form-select border-0" style="border-radius:10px; padding:12px;">
                <option value="laporan_manajer" <?= ($tipeData == 'laporan_manajer') ? 'selected' : '' ?>>Laporan dari Manager</option>
                <option value="transaksi" <?= ($tipeData == 'transaksi') ? 'selected' : '' ?>>Data Transaksi Langsung</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:0.75rem; font-weight:700;">DARI TANGGAL</label>
            <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="form-control border-0" style="border-radius:10px; padding:12px;">
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:0.75rem; font-weight:700;">SAMPAI TANGGAL</label>
            <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="form-control border-0" style="border-radius:10px; padding:12px;">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-dark-green w-100" style="padding:12px; border-radius:10px;">Filter Data</button>
        </div>
    </form>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3 mb-4" style="background:#e8f5e9; color:#2e7d32;">
        <i class="fa-solid fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($tipeData == 'laporan_manajer'): ?>
    <!-- ===== LAPORAN DARI MANAGER ===== -->
    <?php if(!empty($laporanData)): ?>
        <?php foreach($laporanData as $l): ?>
            <?php $reportContent = json_decode($l['content'], true); ?>
            <div class="report-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#112a1f;"><?= esc($l['title']) ?></h4>
                        <div class="text-muted" style="font-size:0.85rem;">
                            <i class="fa-regular fa-calendar me-1"></i> Periode: <?= date('d M Y', strtotime($l['period_start'])) ?> - <?= date('d M Y', strtotime($l['period_end'])) ?>
                            &nbsp;·&nbsp; <span class="badge bg-light text-dark border"><?= esc(ucfirst($l['report_type'])) ?></span>
                            &nbsp;·&nbsp; Dibuat: <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <?php if($l['status'] == 'approved'): ?>
                            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check-circle me-1"></i> Disetujui</span>
                        <?php elseif($l['status'] == 'reviewed'): ?>
                            <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-xmark me-1"></i> Ditolak</span>
                        <?php else: ?>
                            <button class="btn btn-sm btn-success rounded-pill px-3" onclick="approveReport(<?= $l['id'] ?>)"><i class="fa-solid fa-check me-1"></i> Setujui</button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="rejectReport(<?= $l['id'] ?>)"><i class="fa-solid fa-xmark me-1"></i> Tolak</button>
                        <?php endif; ?>
                        <a href="<?= base_url('owner/view_report/'.$l['id']) ?>" class="btn btn-sm btn-dark rounded-pill px-3"><i class="fa-solid fa-eye me-1"></i> Lihat Data</a>
                        <a href="<?= base_url('owner/export_report_excel/'.$l['id']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="fa-solid fa-file-excel me-1"></i> Excel</a>
                    </div>
                </div>

                <?php if($reportContent && isset($reportContent['total_penjualan'])): ?>
                    <!-- KPI Ringkasan -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="kpi-mini">
                                <div class="kpi-mini-val">Rp <?= number_format($reportContent['total_penjualan'], 0, ',', '.') ?></div>
                                <div class="kpi-mini-label">Total Penjualan</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-mini">
                                <div class="kpi-mini-val"><?= $reportContent['total_transaksi'] ?></div>
                                <div class="kpi-mini-label">Total Transaksi</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-mini">
                                <div class="kpi-mini-val"><?= $reportContent['jumlah_pembeli'] ?></div>
                                <div class="kpi-mini-label">Jumlah Pembeli</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kpi-mini" style="background:#e8f5e9;">
                                <div class="kpi-mini-val text-success">Rp <?= number_format($reportContent['total_penjualan'], 0, ',', '.') ?></div>
                                <div class="kpi-mini-label">Keuntungan Kotor</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Detail Pesanan -->
                    <?php if(!empty($reportContent['detail_orders'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">NO ORDER</th>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">PEMBELI</th>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">TOTAL BELANJA</th>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">BAYAR</th>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">LOGISTIK</th>
                                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">TANGGAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reportContent['detail_orders'] as $od): ?>
                                <tr>
                                    <td class="fw-bold font-monospace"><?= esc($od['order_number']) ?></td>
                                    <td class="fw-bold"><?= esc($od['customer_name']) ?></td>
                                    <td class="fw-bold text-success">Rp <?= number_format($od['total_amount'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if($od['payment_status'] == 'paid'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?= esc($od['logistic_status']) ?></span></td>
                                    <td class="text-muted"><?= date('d/m/Y H:i', strtotime($od['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <!-- Daftar Pembeli -->
                    <?php if(!empty($reportContent['daftar_pembeli'])): ?>
                    <div class="mt-3 p-3 rounded-3" style="background:#f7f9fa;">
                        <strong class="text-muted" style="font-size:0.75rem; letter-spacing:1px;">DAFTAR PEMBELI:</strong>
                        <?php foreach($reportContent['daftar_pembeli'] as $buyer): ?>
                            <span class="badge bg-white text-dark border me-1 mt-1 px-2 py-1"><?= esc($buyer) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Laporan lama (content bukan JSON) -->
                    <div class="p-3 rounded-3" style="background:#f7f9fa; line-height:1.7;">
                        <?= nl2br(esc($l['content'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="report-card text-center py-5">
            <i class="fa-solid fa-inbox fa-3x mb-3" style="opacity:0.15;"></i>
            <p class="text-muted fw-bold">Belum ada laporan dari Manager pada periode ini.</p>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- ===== TRANSAKSI LANGSUNG ===== -->
    <div class="report-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0" style="color:#112a1f;">Data Transaksi Real-Time</h5>
            <a href="<?= base_url('owner/export_excel?start_date='.$startDate.'&end_date='.$endDate) ?>" class="btn btn-sm btn-outline-success border-2 fw-bold px-3 py-2 rounded-3">
                <i class="fa-solid fa-file-excel me-1"></i> Unduh Excel
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">NO ORDER</th>
                        <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">PELANGGAN</th>
                        <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">TANGGAL</th>
                        <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">TOTAL</th>
                        <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1px;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transaksiData)): ?>
                        <?php foreach($transaksiData as $t): ?>
                        <tr>
                            <td class="fw-bold font-monospace"><?= esc($t['order_number']) ?></td>
                            <td class="fw-bold"><?= esc($t['customer_name']) ?></td>
                            <td class="text-muted"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                            <td class="fw-bold text-dark">Rp <?= number_format($t['total_amount'], 0, ',', '.') ?></td>
                            <td>
                                <?php if($t['payment_status'] == 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi pada rentang ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function approveReport(id) {
    Swal.fire({
        title: 'Setujui Laporan?',
        text: 'Laporan dari Manager ini akan ditandai sebagai Disetujui.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0b2e21',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Setujui',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url("owner/approve_report/") ?>' + id;
        }
    });
}

function rejectReport(id) {
    Swal.fire({
        title: 'Tolak Laporan?',
        text: 'Laporan ini akan ditandai sebagai Ditolak/Perlu Revisi.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> Ya, Tolak',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url("owner/reject_report/") ?>' + id;
        }
    });
}
</script>
<?= $this->endSection() ?>
