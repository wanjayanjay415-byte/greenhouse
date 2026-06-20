<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<a href="<?= base_url('owner/reports') ?>" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Laba/Rugi</a>
<a href="<?= base_url('owner/users') ?>" class="nav-item active"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
<a href="<?= base_url('owner/monitoring') ?>" class="nav-item"><i class="fa-solid fa-layer-group"></i> Monitoring</a>
<a href="<?= base_url('owner/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4 d-flex justify-content-between align-items-end">
    <div>
        <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">ACCESS MANAGEMENT</div>
        <h2 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif;">Kelola Pengguna</h2>
        <p class="text-muted fs-6 mb-0">Tambah, Edit, dan Kelola akses Customer serta Manager sistem.</p>
    </div>
    <button class="btn btn-dark-green fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-plus me-2"></i> Tambah User Baru
    </button>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3" style="background:#e8f5e9; color:#2e7d32;">
        <strong>Sukses!</strong> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible shadow-sm border-0 rounded-3">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="ps-3 text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px; white-space:nowrap;">IDENTITAS USER</th>
                    <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">ROLE</th>
                    <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">STATUS AKSES</th>
                    <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">BERGABUNG SEJAK</th>
                    <th class="text-muted text-center" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">TINDAKAN KONTROL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td class="ps-3 pt-3 pb-3">
                            <div class="fw-bold text-dark mb-1" style="font-size:1.05rem;"><?= esc($user['full_name']) ?></div>
                            <div style="font-size:0.8rem; color:#888;"><i class="fa-solid fa-envelope me-1"></i> <?= esc($user['email']) ?> &nbsp;|&nbsp; <i class="fa-solid fa-phone me-1"></i> <?= esc($user['phone']) ?></div>
                        </td>
                        <td>
                            <?php if($user['role'] == 'manager'): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill"><i class="fa-solid fa-briefcase me-1"></i> Manager Operasional</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill"><i class="fa-solid fa-basket-shopping me-1"></i> Customer</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($user['status'] == 'active'): ?>
                                <span class="badge bg-success" style="font-weight:600; padding:6px 12px; border-radius:6px;">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger" style="font-weight:600; padding:6px 12px; border-radius:6px;">Blockir</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-weight:500; font-size:0.9rem;"><?= date('d F Y', strtotime($user['created_at'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border text-dark me-1" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" title="Ubah User"><i class="fa-solid fa-pen-to-square"></i></button>
                            <a href="<?= base_url('owner/delete_user/'.$user['id']) ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akses sistem user pengguna ini secara permanen?')" title="Cabut Akses/Hapus"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom mx-3 px-0 py-3 mt-3">
                <h5 class="modal-title fw-bold" style="color:#112a1f;" id="modalTitle"><i class="fa-solid fa-user-plus me-2 text-muted"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('owner/create_user') ?>" method="POST" id="userForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="userId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">NAMA LENGKAP</label>
                        <input type="text" name="full_name" id="userName" class="form-control" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">ALAMAT EMAIL</label>
                        <input type="email" name="email" id="userEmail" class="form-control" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">NOMOR TELEPON</label>
                            <input type="text" name="phone" id="userPhone" class="form-control" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">OTORITAS (ROLE)</label>
                            <select name="role" id="userRole" class="form-select" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;" required>
                                <option value="customer">Pelanggan (Customer)</option>
                                <option value="manager">Manajer Operasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">KATA SANDI</label>
                        <input type="text" name="password" id="userPassword" class="form-control" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;" placeholder="Kosongkan jika tidak ingin mengubah (saat Edit)" required>
                    </div>
                    
                    <div id="statusDiv" class="mb-3" style="display:none;">
                        <label class="form-label text-muted" style="font-size:0.75rem; font-weight:700;">STATUS AKUN</label>
                        <select name="status" id="userStatus" class="form-select" style="border-radius:10px; background:#f4f6f8; border:none; padding:12px;">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif / Blokir</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; padding:10px 20px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-dark-green" style="border-radius:10px; padding:10px 20px;">Simpan Data User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editUser(user) {
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit Data Akses User';
    document.getElementById('userForm').action = '<?= base_url("owner/edit_user") ?>';
    
    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.full_name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userPhone').value = user.phone;
    document.getElementById('userRole').value = user.role;
    
    // Matikan required di password saat mode edit
    document.getElementById('userPassword').required = false;
    
    document.getElementById('statusDiv').style.display = 'block';
    
    if(user.status) {
        document.getElementById('userStatus').value = user.status;
    }
    
    var myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
    myModal.show();
}

// Reset form when modal closed
document.getElementById('addUserModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-plus me-2 text-muted"></i> Tambah User Baru';
    document.getElementById('userForm').action = '<?= base_url("owner/create_user") ?>';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userPassword').required = true;
    document.getElementById('statusDiv').style.display = 'none';
});
</script>
<?= $this->endSection() ?>
