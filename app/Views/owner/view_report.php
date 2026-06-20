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
    .detail-header { background: linear-gradient(135deg, #0b2e21 0%, #1a4a35 100%); border-radius:20px; color:#fff; padding:40px; margin-bottom:30px; position:relative; overflow:hidden; }
    .detail-header::after { content:''; position:absolute; top:-30px; right:-30px; width:150px; height:150px; background:rgba(255,255,255,0.03); border-radius:50%; }
    .kpi-box { background:rgba(255,255,255,0.1); border-radius:14px; padding:24px; text-align:center; backdrop-filter:blur(10px); }
    .kpi-box-val { font-size:1.8rem; font-weight:800; color:#fff; }
    .kpi-box-label { font-size:0.7rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-top:6px; }
    .data-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.02); border:1px solid #f0f0f0; overflow:hidden; }
</style>

<!-- Back Button -->
<a href="<?= base_url('owner/reports') ?>" class="btn btn-outline-dark rounded-pill px-4 mb-4" style="border-width:2px;"><i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Laporan</a>

<!-- Header -->
<div class="detail-header">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div style="font-size:0.7rem; font-weight:700; letter-spacing:2px; color:rgba(255,255,255,0.6); margin-bottom:10px;">LAPORAN #<?= $report['id'] ?></div>
            <h2 class="fw-bold mb-2" style="font-family:'Playfair Display', serif; font-size:2rem;"><?= esc($report['title']) ?></h2>
            <div style="font-size:0.9rem; opacity:0.8;">
                <i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($report['period_start'])) ?> – <?= date('d M Y', strtotime($report['period_end'])) ?>
                &nbsp;·&nbsp; Tipe: <?= ucfirst(esc($report['report_type'])) ?>
                &nbsp;·&nbsp; Dibuat: <?= date('d M Y, H:i', strtotime($report['created_at'])) ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if($report['status'] == 'approved'): ?>
                <span class="badge bg-success px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-check-circle me-1"></i> Disetujui</span>
            <?php elseif($report['status'] == 'reviewed'): ?>
                <span class="badge bg-danger px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-xmark me-1"></i> Ditolak</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Review Anda</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if($data): ?>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="kpi-box-val">Rp <?= number_format($data['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
                <div class="kpi-box-label">Total Penjualan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="kpi-box-val"><?= $data['total_transaksi'] ?? 0 ?></div>
                <div class="kpi-box-label">Total Transaksi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="kpi-box-val"><?= $data['jumlah_pembeli'] ?? 0 ?></div>
                <div class="kpi-box-label">Pembeli Unik</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box" style="background:rgba(188,240,218,0.2);">
                <div class="kpi-box-val" style="color:#bcf0da;">Rp <?= number_format($data['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
                <div class="kpi-box-label">Keuntungan Kotor</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if(!empty($data['catatan'])): ?>
<div class="data-card p-4 mb-4" style="background:#fffaf0; border-left:5px solid #d97706;">
    <h6 class="fw-bold mb-3" style="color:#d97706;"><i class="fa-solid fa-note-sticky me-2"></i> Catatan Manajer</h6>
    <div style="font-size:0.95rem; line-height:1.6; color:#555;">
        <?= nl2br(esc($data['catatan'])) ?>
    </div>
</div>
<?php endif; ?>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0" style="color:#112a1f;"><i class="fa-solid fa-table-list me-2 text-muted"></i> Rincian Data Pesanan Terjual</h4>
    <div class="d-flex gap-2">
        <?php if($data && !empty($data['detail_orders'])): ?>
            <a href="<?= base_url('owner/export_report_excel/'.$report['id']) ?>" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-file-excel me-2"></i> Export ke Excel
            </a>
            <button class="btn btn-dark rounded-pill px-4 fw-bold" onclick="window.print()">
                <i class="fa-solid fa-print me-2"></i> Cetak / PDF
            </button>
        <?php endif; ?>
        <?php if($report['status'] == 'submitted'): ?>
            <button class="btn btn-success rounded-pill px-4 fw-bold" onclick="approveReport(<?= $report['id'] ?>)">
                <i class="fa-solid fa-check me-2"></i> Setujui Laporan
            </button>
            <button class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="rejectReport(<?= $report['id'] ?>)">
                <i class="fa-solid fa-xmark me-2"></i> Tolak
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if($data && !empty($data['detail_orders'])): ?>
<!-- Tabel Pesanan -->
<div class="data-card mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th class="ps-4 text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 24px;">NO</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">NOMOR ORDER</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">NAMA PEMBELI</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">TOTAL BELANJA</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">STATUS BAYAR</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">STATUS LOGISTIK</th>
                    <th class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:1.5px; padding:18px 16px;">TANGGAL TRANSAKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['detail_orders'] as $idx => $od): ?>
                <tr>
                    <td class="ps-4 fw-bold text-muted" style="padding:18px 24px;"><?= $idx + 1 ?></td>
                    <td class="fw-bold font-monospace" style="padding:18px 16px; color:#112a1f;"><?= esc($od['order_number']) ?></td>
                    <td class="fw-bold" style="padding:18px 16px;"><?= esc($od['customer_name']) ?></td>
                    <td class="fw-bold text-success" style="padding:18px 16px; font-size:1.05rem;">Rp <?= number_format($od['total_amount'], 0, ',', '.') ?></td>
                    <td style="padding:18px 16px;">
                        <?php if($od['payment_status'] == 'paid'): ?>
                            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check-circle me-1"></i> Lunas</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:18px 16px;">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?= esc($od['logistic_status']) ?></span>
                    </td>
                    <td class="text-muted" style="padding:18px 16px;"><?= date('d M Y, H:i', strtotime($od['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background:#f0f7f3;">
                <tr>
                    <td colspan="3" class="ps-4 fw-bold text-dark" style="padding:18px 24px; font-size:1.05rem;">GRAND TOTAL</td>
                    <td class="fw-bold text-success" style="padding:18px 16px; font-size:1.2rem;">Rp <?= number_format($data['total_penjualan'], 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Daftar Pembeli -->
<?php if(!empty($data['daftar_pembeli'])): ?>
<div class="data-card p-4 mb-4">
    <h6 class="fw-bold mb-3" style="color:#112a1f;"><i class="fa-solid fa-users me-2 text-muted"></i> Daftar Pembeli Dalam Periode Ini</h6>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach($data['daftar_pembeli'] as $buyer): ?>
            <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:#f7f9fa; border:1px solid #eaeaea;">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width:28px; height:28px; background:#112a1f; font-size:0.7rem;"><?= substr($buyer, 0, 1) ?></div>
                <span class="fw-bold" style="font-size:0.9rem;"><?= esc($buyer) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php else: ?>
<!-- Laporan Lama / Content Teks -->
<div class="data-card p-5 text-center">
    <i class="fa-solid fa-file-lines fa-3x mb-3" style="opacity:0.15;"></i>
    <p class="text-muted fw-bold mb-3">Laporan ini tidak memiliki data terstruktur (dikirim sebelum sistem dirombak).</p>
    <div class="p-4 rounded-3 text-start mx-auto" style="background:#f7f9fa; max-width:700px; line-height:1.8;">
        <?= nl2br(esc($report['content'])) ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function approveReport(id) {
    Swal.fire({
        title: 'Setujui Laporan?',
        text: 'Laporan ini akan ditandai sebagai Disetujui oleh Owner.',
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
        text: 'Laporan ini akan ditandai sebagai Ditolak / Perlu Revisi.',
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
