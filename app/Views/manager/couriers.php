<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item active"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .breadcrumb-path { font-size: 0.75rem; font-weight: 700; letter-spacing: 1.5px; margin-bottom: 12px; }
    .breadcrumb-path span { color: #888; }
    .breadcrumb-path strong { color: #112a1f; margin-left: 8px;}

    .btn-dark-green { background: #0b2e21; color: #fff; border: none; padding: 12px 24px; font-weight: 600; border-radius: 8px; font-size: 0.85rem;}

    .table-container { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .table-db th { font-size: 0.7rem; color: #555; font-weight: 800; letter-spacing: 1.5px; padding: 24px 20px 20px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;}
    .table-db td { padding: 20px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }

    .ava-circle { width:44px; height:44px; background:#e8ece2; color:#112a1f; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:800; font-size:1rem;}
    .region-pill { background:#dbeafe; color:#1e40af; padding:6px 14px; border-radius:20px; font-size:0.72rem; font-weight:800; letter-spacing:0.5px; display:inline-block;}

    .status-dot { width: 8px; height: 8px; border-radius: 50%; display:inline-block; margin-right:8px;}
    .s-aktif { background: #2e7d32; }
    .s-off { background: #b0bec5; }

    .action-btn { width: 36px; height: 36px; background: #fff; border: 1px solid #eaeaea; border-radius: 8px; color: #112a1f; display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; text-decoration:none; transition:0.2s; cursor:pointer;}
    .action-btn:hover { background: #f0f0f0; }

    .metric-card-u { border-radius: 20px; padding: 30px; height: 100%; position:relative; overflow:hidden;}
    .mu-dark { background: #0b2e21; color: #fff; }
    .mu-white { background: #fff; color: #112a1f; border:1px solid #f0f0f0;}

    .form-ctrl-gh { border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600; }
    .form-lbl-gh { font-weight:800; color:#777; font-size:0.72rem; letter-spacing:1px; margin-bottom:8px; text-transform:uppercase; }
</style>

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

<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <div class="breadcrumb-path"><span>LOGISTIK ></span> <strong>COURIER DIRECTORY</strong></div>
        <h1 class="fw-bold mb-2" style="color: #112a1f; font-size:2.8rem; letter-spacing:-1px;">Kelola Kurir / Driver</h1>
        <p class="text-muted fs-6 mb-0" style="max-width:560px; line-height:1.6;">Manajemen data kurir dan wilayah tugas. Setiap kurir menangani pesanan sesuai domisili/alamat pelanggan dalam satu kota.</p>
    </div>
    <button class="btn btn-dark-green shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourierModal"><i class="fa-solid fa-truck-plus me-2"></i> TAMBAH KURIR</button>
</div>

<!-- Metric Cards -->
<div class="row g-4 mb-5 justify-content-center">
    <div class="col-lg-6">
        <div class="metric-card-u mu-dark">
            <div style="font-size:0.75rem; font-weight:800; letter-spacing:1px; opacity:0.8; margin-bottom:12px;">TOTAL KURIR</div>
            <div style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; margin-bottom:16px;"><?= $totalCouriers ?></div>
            <div style="font-size:0.85rem; font-weight:700;"><i class="fa-solid fa-truck-fast me-2"></i> Total kurir terdaftar di sistem distribusi</div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="metric-card-u mu-white">
            <div style="font-size:0.75rem; font-weight:800; letter-spacing:1px; color:#555; margin-bottom:12px;">KURIR AKTIF</div>
            <div style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; margin-bottom:16px;"><?= $countActive ?></div>
            <div style="font-size:0.85rem; font-weight:700; color:#2e7d32;"><i class="fa-solid fa-circle-check me-2"></i> Siap menangani pengiriman</div>
        </div>
    </div>
</div>

<!-- Tabel Kurir -->
<div class="table-container mb-5">
    <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-2">
        <h4 class="fw-bold m-0" style="color:#112a1f;">Daftar Kurir</h4>
        <span class="text-muted fw-bold" style="font-size:0.8rem;"><?= $totalCouriers ?> kurir</span>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless table-db align-middle mb-0">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="22%">NAMA KURIR</th>
                    <th width="15%">NO. HP</th>
                    <th width="20%">ALAMAT</th>
                    <th width="14%">WILAYAH TUGAS</th>
                    <th width="10%">PESANAN AKTIF</th>
                    <th width="8%">STATUS</th>
                    <th width="6%" class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($couriers)): ?>
                    <?php foreach($couriers as $row): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $row['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="ava-circle"><i class="fa-solid fa-motorcycle"></i></div>
                                <div class="fw-bold text-dark fs-6"><?= esc($row['name']) ?></div>
                            </div>
                        </td>
                        <td class="text-dark" style="font-size:0.85rem;"><?= esc($row['phone'] ?? '-') ?></td>
                        <td class="text-muted" style="font-size:0.82rem;"><?= esc($row['address'] ?? '-') ?></td>
                        <td><span class="region-pill"><i class="fa-solid fa-location-dot me-1"></i><?= esc($row['region']) ?></span></td>
                        <td class="fw-bold text-dark text-center"><?= $row['active_orders'] ?></td>
                        <td>
                            <?php if ($row['status'] == 'active'): ?>
                                <span class="fw-bold" style="color:#2e7d32; font-size:0.85rem;"><span class="status-dot s-aktif"></span> Aktif</span>
                            <?php else: ?>
                                <span class="fw-bold text-muted" style="font-size:0.85rem;"><span class="status-dot s-off"></span> Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="action-btn border-0 text-primary" title="Edit Kurir" data-bs-toggle="modal" data-bs-target="#editCourierModal<?= $row['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn border-0 text-danger" title="Hapus Kurir" onclick='confirmDelete(<?= json_encode(base_url('manager/delete_courier/'.$row['id'])) ?>, <?= json_encode($row['name']) ?>)'><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fa-solid fa-truck-ramp-box fa-3x mb-3 d-block" style="opacity:0.3;"></i>Belum ada kurir terdaftar. Klik <strong>Tambah Kurir</strong> untuk memulai.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center pt-4 pb-4 px-4 border-top">
        <div class="text-muted" style="font-size:0.85rem; font-weight:600;">Menampilkan <?= count($couriers) ?> dari <?= $totalCouriers ?> kurir</div>
    </div>
</div>

<!-- Modal Tambah Kurir -->
<div class="modal fade" id="addCourierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;"><i class="fa-solid fa-truck-plus me-2"></i> Tambah Kurir Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('manager/create_courier') ?>" method="POST" data-turbo="false">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-lbl-gh">Nama Kurir</label>
                        <input type="text" class="form-control form-ctrl-gh" name="name" required placeholder="Nama lengkap kurir">
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl-gh">No. HP</label>
                        <input type="text" class="form-control form-ctrl-gh" name="phone" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl-gh">Alamat</label>
                        <input type="text" class="form-control form-ctrl-gh" name="address" placeholder="Alamat domisili kurir">
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-lbl-gh">Wilayah Tugas</label>
                            <input type="text" class="form-control form-ctrl-gh" name="region" required placeholder="Mis. Bekasi Timur">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-lbl-gh">Status</label>
                            <select class="form-select form-ctrl-gh" name="status">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-light border small mb-0" style="border-radius:10px;">
                        <i class="fa-solid fa-circle-info me-1 text-primary"></i> <strong>Wilayah</strong> dipakai untuk menyarankan kurir saat alamat pesanan cocok dengan wilayah ini.
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-dark-green"><i class="fa-solid fa-plus me-2"></i> Tambah Kurir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kurir (1 per kurir) -->
<?php if(!empty($couriers)): ?>
<?php foreach($couriers as $row): ?>
<div class="modal fade" id="editCourierModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;"><i class="fa-solid fa-pen-to-square me-2"></i> Edit: <?= esc($row['name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('manager/edit_courier') ?>" method="POST" data-turbo="false">
                <?= csrf_field() ?>
                <input type="hidden" name="courier_id" value="<?= $row['id'] ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-lbl-gh">Nama Kurir</label>
                        <input type="text" class="form-control form-ctrl-gh" name="name" required value="<?= esc($row['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl-gh">No. HP</label>
                        <input type="text" class="form-control form-ctrl-gh" name="phone" value="<?= esc($row['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl-gh">Alamat</label>
                        <input type="text" class="form-control form-ctrl-gh" name="address" value="<?= esc($row['address'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-lbl-gh">Wilayah Tugas</label>
                            <input type="text" class="form-control form-ctrl-gh" name="region" required value="<?= esc($row['region']) ?>">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-lbl-gh">Status</label>
                            <select class="form-select form-ctrl-gh" name="status">
                                <option value="active" <?= $row['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= $row['status'] == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-dark-green"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
