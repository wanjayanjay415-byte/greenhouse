<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item active"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .breadcrumb-path { font-size: 0.75rem; font-weight: 700; letter-spacing: 1.5px; margin-bottom: 12px; }
    .breadcrumb-path span { color: #888; }
    .breadcrumb-path strong { color: #112a1f; margin-left: 8px;}
    
    .btn-dark-green { background: #0b2e21; color: #fff; border: none; padding: 12px 24px; font-weight: 600; border-radius: 8px; font-size: 0.85rem;}
    .btn-outline-dark-green { border: 1px solid #dcdcdc; background: #fff; color: #112a1f; padding: 12px 24px; font-weight: 700; border-radius: 8px; font-size: 0.85rem;}
    
    .table-container { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .table-db th { font-size: 0.7rem; color: #555; font-weight: 800; letter-spacing: 1.5px; padding: 24px 20px 20px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;}
    .table-db td { padding: 20px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    
    .ava-circle { width:44px; height:44px; background:#e8ece2; color:#112a1f; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:800; font-size:1rem;}
    
    .role-pill { padding: 6px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; letter-spacing:0.5px; min-width:80px; text-align:center; display:inline-block;}
    .role-customer { background: #bcf0da; color: #0b2e21; }
    .role-manager { background: #eaeaea; color: #555; }
    .role-owner { background: #e0dcd2; color: #5c5545; }
    .role-admin { background: #dbeafe; color: #1e40af; }
    
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display:inline-block; margin-right:8px;}
    .s-aktif { background: #2e7d32; }
    .s-offline { background: #b0bec5; }
    .s-suspend { background: #c62828; }

    .action-btn { width: 36px; height: 36px; background: #fff; border: 1px solid #eaeaea; border-radius: 8px; color: #112a1f; display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; text-decoration:none; transition:0.2s; cursor:pointer;}
    .action-btn:hover { background: #f0f0f0; }

    .metric-card-u { border-radius: 20px; padding: 30px; height: 100%; position:relative; overflow:hidden;}
    .mu-dark { background: #0b2e21; color: #fff; }
    .mu-white { background: #fff; color: #112a1f; border:1px solid #f0f0f0;}
    .mu-beige { background: #eeecd8; color: #112a1f; }
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
        <div class="breadcrumb-path">
            <span>ARCHIVE ></span> <strong>USER DIRECTORY</strong>
        </div>
        <h1 class="fw-bold mb-2" style="color: #112a1f; font-size:2.8rem; letter-spacing:-1px;">Kelola Akun Pengguna</h1>
        <p class="text-muted fs-6 mb-0" style="max-width:500px; line-height:1.6;">Manajemen hak akses, registrasi, dan pengelolaan seluruh akun pelanggan dalam ekosistem Greenhouse.</p>
    </div>
    <button class="btn btn-dark-green shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-user-plus me-2"></i> TAMBAH USER BARU</button>
</div>

<!-- Metric Cards Real -->
<div class="row g-4 mb-5 justify-content-center">
    <div class="col-lg-6">
        <div class="metric-card-u mu-dark">
            <div style="font-size:0.75rem; font-weight:800; letter-spacing:1px; opacity:0.8; margin-bottom:12px;">TOTAL PELANGGAN</div>
            <div style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; margin-bottom:16px;"><?= $totalUsers ?></div>
            <div style="font-size:0.85rem; font-weight:700;"><i class="fa-solid fa-users me-2"></i> Total akun pelanggan terdaftar di GreenHouse</div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="metric-card-u mu-white">
            <div style="font-size:0.75rem; font-weight:800; letter-spacing:1px; color:#555; margin-bottom:12px;">STATUS AKTIVITAS</div>
            <div style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; margin-bottom:16px;"><?= $countActive ?></div>
            <div style="font-size:0.85rem; font-weight:700; color:#2e7d32;"><i class="fa-solid fa-user-check me-2"></i> Aktif · <span class="text-danger"><i class="fa-solid fa-user-lock ms-1 me-1"></i> <?= $countSuspend ?> Ditangguhkan</span></div>
        </div>
    </div>
</div>

<!-- Tabel User -->
<div class="table-container mb-5">
    <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-2">
        <h4 class="fw-bold m-0" style="color:#112a1f;">Daftar Semua Pengguna</h4>
        <span class="text-muted fw-bold" style="font-size:0.8rem;"><?= $totalUsers ?> users</span>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless table-db align-middle mb-0">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="25%">NAMA & EMAIL</th>
                    <th width="15%">TELEPON</th>
                    <th width="12%">PERAN</th>
                    <th width="13%">TERDAFTAR</th>
                    <th width="12%">STATUS</th>
                    <th width="18%" class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach($users as $idx => $row): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= sprintf('%02d', $idx + 1) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="ava-circle" style="background:#e4e2c7;"><?= strtoupper(substr($row['full_name'], 0, 2)) ?></div>
                                <div>
                                    <div class="fw-bold text-dark fs-6"><?= esc($row['full_name']) ?></div>
                                    <div class="text-muted" style="font-size:0.8rem;"><?= esc($row['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-dark" style="font-size:0.85rem;"><?= esc($row['phone'] ?? '-') ?></td>
                        <td>
                            <span class="role-pill role-customer">CUSTOMER</span>
                        </td>
                        <td class="text-dark" style="font-size:0.85rem; font-weight:600;"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($row['status'] == 'active'): ?>
                                <span class="fw-bold" style="color:#2e7d32; font-size:0.85rem;"><span class="status-dot s-aktif"></span> Aktif</span>
                            <?php elseif ($row['status'] == 'suspended'): ?>
                                <span class="fw-bold text-danger" style="font-size:0.85rem;"><span class="status-dot s-suspend"></span> Ditangguhkan</span>
                            <?php else: ?>
                                <span class="fw-bold text-muted" style="font-size:0.85rem;"><span class="status-dot s-offline"></span> Offline</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="action-btn border-0 text-primary" title="Edit User" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $row['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn border-0 text-danger" title="Hapus User" onclick='confirmDelete(<?= json_encode(base_url('manager/delete_user/'.$row['id'])) ?>, <?= json_encode($row['full_name']) ?>)'><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fa-solid fa-users-slash fa-3x mb-3 d-block" style="opacity:0.3;"></i>Belum ada user terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-0 pt-4 pb-4 px-4 border-top">
        <div class="text-muted" style="font-size:0.85rem; font-weight:600;">Menampilkan <?= count($users) ?> dari <?= $totalUsers ?> pengguna</div>
    </div>
</div>

<!-- Modal Tambah User Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;"><i class="fa-solid fa-user-plus me-2"></i> Registrasi User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('manager/create_user') ?>" method="POST" data-turbo="false">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NAMA LENGKAP</label>
                        <input type="text" class="form-control" name="full_name" required placeholder="Nama Lengkap" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">EMAIL</label>
                        <input type="email" class="form-control" name="email" required placeholder="email@greenhouse.com" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NO. TELEPON</label>
                        <input type="text" class="form-control" name="phone" placeholder="08xxxxxxxxxx" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">PASSWORD</label>
                            <input type="password" class="form-control" name="password" required placeholder="Min. 6 karakter" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-dark-green"><i class="fa-solid fa-user-plus me-2"></i> Daftarkan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User (1 per user) -->
<?php if(!empty($users)): ?>
<?php foreach($users as $row): ?>
<div class="modal fade" id="editUserModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;"><i class="fa-solid fa-pen-to-square me-2"></i> Edit: <?= esc($row['full_name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('manager/edit_user') ?>" method="POST" data-turbo="false">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NAMA LENGKAP</label>
                        <input type="text" class="form-control" name="full_name" required value="<?= esc($row['full_name']) ?>" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">EMAIL</label>
                        <input type="email" class="form-control" name="email" required value="<?= esc($row['email']) ?>" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NO. TELEPON</label>
                        <input type="text" class="form-control" name="phone" value="<?= esc($row['phone'] ?? '') ?>" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">STATUS</label>
                            <select class="form-select" name="status" required style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                                <option value="active" <?= $row['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="offline" <?= $row['status'] == 'offline' ? 'selected' : '' ?>>Offline</option>
                                <option value="suspended" <?= $row['status'] == 'suspended' ? 'selected' : '' ?>>Ditangguhkan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">GANTI PASSWORD</label>
                            <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diubah" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
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
