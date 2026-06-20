<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<a href="<?= base_url('owner/reports') ?>" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Laba/Rugi</a>
<a href="<?= base_url('owner/users') ?>" class="nav-item"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
<a href="<?= base_url('owner/monitoring') ?>" class="nav-item"><i class="fa-solid fa-layer-group"></i> Monitoring</a>
<a href="<?= base_url('owner/settings') ?>" class="nav-item active"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">PREFERENSI & ADMINISTRASI SISTEM</div>
    <h2 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif;">Pengaturan Bisnis</h2>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3" style="background:#e8f5e9; color:#2e7d32;">
        <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible shadow-sm border-0 rounded-3">
        <strong>Peringatan Sistem:</strong> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Edit Profil Owner -->
    <div class="col-lg-6">
        <div class="card-custom">
            <h5 class="fw-bold mb-4" style="color:#112a1f;"><i class="fa-solid fa-address-card text-muted me-2"></i> Identitas & Kredensial Owner</h5>
            
            <form action="<?= base_url('owner/update_profile') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">NAMA PEMILIK/OWNER</label>
                    <input type="text" name="full_name" class="form-control" style="background:#f4f6f8; border:none; padding:12px; border-radius:10px;" value="<?= esc($userName) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">ALAMAT EMAIL RESMI</label>
                    <input type="email" name="email" class="form-control" style="background:#f4f6f8; border:none; padding:12px; border-radius:10px;" required>
                    <div class="form-text" style="font-size:0.75rem;">Email ini digunakan untuk login portal. Isi ulang untuk verifikasi pengubahan.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">NOMOR TELEPON VALID</label>
                    <input type="text" name="phone" class="form-control" style="background:#f4f6f8; border:none; padding:12px; border-radius:10px;" required>
                </div>
                <div class="mb-4 pt-3 border-top">
                    <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">GANTI KATA SANDI (OPSIONAL)</label>
                    <input type="password" name="password" class="form-control" style="background:#f4f6f8; border:none; padding:12px; border-radius:10px;" placeholder="Kosongkan jika tidak ingin mengubah sandi">
                </div>
                <button type="submit" class="btn btn-dark-green w-100" style="padding:12px; border-radius:10px; font-weight:700; letter-spacing:1px;"><i class="fa-solid fa-save me-2"></i> TERAPKAN PERUBAHAN</button>
            </form>
        </div>
    </div>
    
    <!-- System Backup -->
    <div class="col-lg-6">
        <div class="card-custom" style="background:linear-gradient(135deg, #2c1011 0%, #4a2123 100%); color:#fff; display:flex; flex-direction:column;">
            <div class="mb-4">
                <i class="fa-solid fa-server fs-1 text-danger opacity-50 mb-3"></i>
                <h4 class="fw-bold text-white mb-2" style="font-family:'Playfair Display', serif;">Pemeliharaan & Backup Basis Data</h4>
                <p style="font-size:0.9rem; opacity:0.8; line-height:1.6;">
                    Gunakan fitur ini secara rutin untuk mengunduh seluruh rekaman transaksi (stok, pengguna, order) di dalam sistem <code class="bg-dark px-2 py-1 text-white rounded opacity-75">db_greenhouse</code> MYSQL ke komputer lokal (Backup SQL Dump) guna mencegah kehilangan data.
                </p>
            </div>
            
            <div class="mt-auto p-4 rounded-3" style="background:rgba(0,0,0,0.2);">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-solid fa-shield-halved text-success fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold" style="font-size:0.85rem; letter-spacing:1px; color:#bcf0da;">STATUS SISTEM MySQL</div>
                        <div style="font-size:1.1rem; font-weight:700;">Connected / Siap Ekspor</div>
                    </div>
                </div>
                
                <a href="<?= base_url('owner/backup_db') ?>" onclick="return confirm('Mengekspor database utuh (.sql). Proses ini memakan waktu beberapa detik, Lanjutkan?')" class="btn btn-danger w-100 fw-bold border-0 shadow-sm" style="padding:15px; border-radius:10px; background:#e53935; text-transform:uppercase; letter-spacing:1px;">
                    <i class="fa-solid fa-download me-2"></i> Ekspor Full Database Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
