<?= $this->extend('layout/customer') ?>

<?= $this->section('content') ?>
<style>
    .profile-header { background: linear-gradient(135deg, #0b2e21 0%, #1a4a35 100%); color: #fff; padding: 60px 0 100px 0; text-align: center; }
    .avatar-lg { width: 120px; height: 120px; border-radius: 50%; font-size: 3rem; background-color: #fff; color: #0b2e21; display: flex; align-items: center; justify-content: center; margin: -60px auto 20px; border: 5px solid #fcfcfc; box-shadow: 0 10px 30px rgba(0,0,0,0.1); font-weight:800; font-family:'Playfair Display', serif;}
    .card-settings { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
    .nav-settings .nav-link { color: #555; padding: 16px 24px; font-weight: 500; border-radius: 0; border-left: 3px solid transparent; text-align: left; }
    .nav-settings .nav-link.active { color: #0b2e21; background: #eef2f0; border-left: 3px solid #0b2e21; font-weight: 700; }
    .nav-settings .nav-link:hover:not(.active) { background: #f9f9f9; }
    
    .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #0b2e21; }
    input:checked + .slider:before { transform: translateX(24px); }
</style>

<div class="profile-header">
    <h2 class="Playfair">Pengaturan Akun</h2>
    <p class="opacity-75">Kelola identitas, alamat, dan preferensi keamanan Anda.</p>
</div>

<div class="container" style="margin-top:-60px; margin-bottom:80px;">
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible mb-4 shadow-sm border-0 rounded-3">
            <i class="fa-solid fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible mb-4 shadow-sm border-0 rounded-3">
            <i class="fa-solid fa-circle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar Menu -->
        <div class="col-lg-3">
            <div class="card-settings h-100">
                <!-- Avatar -->
                <div class="text-center pt-5 pb-4 border-bottom">
                    <div class="avatar-lg bg-light text-dark">
                        <?= substr($user['full_name'], 0, 1) ?>
                    </div>
                    <h5 class="fw-bold mb-1" style="color:#112a1f;"><?= esc($user['full_name']) ?></h5>
                    <div class="text-muted small"><?= esc($user['email']) ?></div>
                    <div class="badge bg-success-subtle text-success mt-2">Verified Customer</div>
                </div>
                
                <div class="nav flex-column nav-pills nav-settings" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  <button class="nav-link active" id="informasi-tab" data-bs-toggle="pill" data-bs-target="#informasi" type="button" role="tab"><i class="fa-regular fa-id-card me-2 opacity-50"></i> Informasi Pribadi</button>
                  <button class="nav-link" id="alamat-tab" data-bs-toggle="pill" data-bs-target="#alamat" type="button" role="tab"><i class="fa-solid fa-map-location-dot me-2 opacity-50"></i> Info Pengiriman</button>
                  <button class="nav-link" id="keamanan-tab" data-bs-toggle="pill" data-bs-target="#keamanan" type="button" role="tab"><i class="fa-solid fa-shield-halved me-2 opacity-50"></i> Keamanan & Sandi</button>
                </div>
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="col-lg-9">
            <div class="card-settings p-4 p-md-5 h-100">
                <form action="<?= base_url('updateProfile') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="tab-content" id="v-pills-tabContent">
                      
                      <!-- TAB INFORMASI PRIBADI -->
                      <div class="tab-pane fade show active" id="informasi" role="tabpanel">
                        <h4 class="fw-bold mb-4" style="color:#112a1f;">Informasi Pribadi</h4>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NAMA LENGKAP</label>
                                <input type="text" name="full_name" class="form-control" style="background:#f9f9f9; border:none; padding:15px; border-radius:10px;" value="<?= esc($user['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NOMOR TELEPON (WHATSAPP)</label>
                                <input type="text" name="phone" class="form-control" style="background:#f9f9f9; border:none; padding:15px; border-radius:10px;" value="<?= esc($user['phone']) ?>" required>
                            </div>
                        </div>
                        <hr class="my-5 border-light">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom px-5">Simpan Perubahan</button>
                        </div>
                      </div>
                      
                      <!-- TAB ALAMAT DETAILS -->
                      <div class="tab-pane fade" id="alamat" role="tabpanel">
                        <h4 class="fw-bold mb-2" style="color:#112a1f;">Alamat Detail Pengiriman</h4>
                        <p class="text-muted mb-4 small">Alamat ini akan digunakan secara otomatis saat Anda melakukan _Checkout_.</p>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">ALAMAT LENGKAP</label>
                            <textarea name="address" class="form-control" rows="4" style="background:#f9f9f9; border:none; padding:15px; border-radius:10px;" placeholder="Contoh: Jl. Sudirman No 12, RT 01 RW 02, Kecamatan, Kota, Kodepos. (Patokan: Depan Toko Merah)"><?= esc($user['address'] ?? '') ?></textarea>
                        </div>
                        
                        <hr class="my-5 border-light">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom px-5">Simpan Alamat</button>
                        </div>
                      </div>
                      
                      <!-- TAB KEAMANAN & A2F -->
                      <div class="tab-pane fade" id="keamanan" role="tabpanel">
                        <h4 class="fw-bold mb-4" style="color:#112a1f;">Keamanan Akun</h4>
                        
                        <div class="p-4 rounded-3 mb-4" style="background:#f7f9fa; border:1px solid #eaeaea;">
                            <h6 class="fw-bold mb-3">Ubah Alamat Email</h6>
                            <input type="email" name="email" class="form-control mb-2" style="border:none; padding:15px; border-radius:10px;" value="<?= esc($user['email']) ?>" required>
                            <div class="form-text small">Jika Anda merubah email, Anda harus menggunakan email baru ini untuk login berikutnya.</div>
                        </div>
                        
                        <div class="p-4 rounded-3 mb-4" style="background:#f7f9fa; border:1px solid #eaeaea;">
                            <h6 class="fw-bold mb-3">Ganti Kata Sandi</h6>
                            <input type="password" name="password" class="form-control mb-2" style="border:none; padding:15px; border-radius:10px;" placeholder="Masukkan sandi baru (kosongkan jika tidak ingin merubah)">
                        </div>
                        
                        <div class="p-4 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="background:#f4faf6; border:1px solid #d1e7dd;">
                            <div>
                                <h6 class="fw-bold mb-1 text-success"><i class="fa-solid fa-lock me-2"></i>Autentikasi Dua Faktor (A2F)</h6>
                                <div class="small text-muted">Tingkatkan keamanan dengan mengirimkan kode khusus ke email setiap kali login.</div>
                            </div>
                            <div>
                                <label class="switch">
                                  <input type="hidden" name="two_factor_enabled" value="0">
                                  <input type="checkbox" name="two_factor_enabled" value="1" <?= ($user['two_factor_enabled'] == 1) ? 'checked' : '' ?>>
                                  <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <hr class="my-5 border-light">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom px-5">Perbarui Keamanan</button>
                        </div>
                      </div>
                      
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
