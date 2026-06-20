<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item active"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .k-card { border-radius: 20px; padding: 30px; height: 100%; border: none; }
    .btn-outline-dark-green { border: 1px solid #dcdcdc; background: #fff; color: #112a1f; padding: 12px 24px; font-weight: 700; border-radius: 8px; font-size: 0.85rem; letter-spacing: 0.5px;}
    .btn-dark-green { background: #0b2e21; color: #fff; border: none; padding: 12px 24px; font-weight: 600; border-radius: 8px; font-size: 0.85rem;}
    
    .card-mint { background-color: #e8f9ef; color: #112a1f; }
    .card-dark { background-color: #0b2e21; color: #fff; }
    .card-beige { background-color: #eeecd8; color: #112a1f; }
    
    .table-container { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .table-inv th { font-size: 0.75rem; color: #999; font-weight: 700; letter-spacing: 1.5px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;}
    .table-inv td { padding: 20px 0; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .plant-img { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; margin-right: 16px; }
    
    .pill-green { background: #bcf0da; color: #0b2e21; font-weight: 700; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; letter-spacing: 1px;}
    .pill-red { background: #fee2e2; color: #dc2626; font-weight: 700; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; letter-spacing: 1px;}
    .pill-yellow { background: #fef3c7; color: #d97706; font-weight: 700; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; letter-spacing: 1px;}
    .action-btn { width: 36px; height: 36px; background: #fff; border: 1px solid #eaeaea; border-radius: 8px; color: #112a1f; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; text-decoration:none; transition:0.2s; cursor:pointer;}
    .action-btn:hover { background: #f0f0f0; }

    .bottom-card { background: #eff1f0; border-radius: 20px; padding: 30px; display:flex; align-items:flex-start; gap:20px; }
    .bc-icon { width:48px; height:48px; border-radius:12px; background:#fff; color:#0b2e21; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;}
</style>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: 2px; color: #112a1f; margin-bottom: 8px;">STOCK REPOSITORY</div>
        <h1 class="fw-bold m-0" style="color: #112a1f; font-family:'Playfair Display', serif; font-size:3rem; line-height:1.1; letter-spacing:-1px;">Manajemen Stok &<br>Hasil Tani</h1>
    </div>
    <div class="d-flex gap-3">
        <button class="btn btn-outline-dark-green shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fa-solid fa-leaf me-2"></i> TAMBAH SAYUR BARU</button>
        <button class="btn btn-dark-green shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addStockModal"><i class="fa-solid fa-plus me-2"></i> UPDATE STOK</button>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success fw-bold border-0 shadow-sm mb-4" style="border-radius:12px;">
        <i class="fa-solid fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger fw-bold border-0 shadow-sm mb-4" style="border-radius:12px;">
        <i class="fa-solid fa-xmark-circle me-2"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="row g-4 mb-5">
    <div class="col-lg-5">
        <div class="k-card card-mint position-relative overflow-hidden shadow-sm">
            <div style="font-size:0.75rem; font-weight:700; letter-spacing:1px; opacity:0.8; margin-bottom:16px;">STOK TERSEDIA (KG)</div>
            <div class="d-flex align-items-baseline mb-4">
                <span style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px;"><?= number_format($totalStokKg, 0, ',', '.') ?></span>
                <span class="ms-2 fs-4 text-muted">kg</span>
            </div>
            <div class="fw-bold" style="font-size:0.85rem;">
                <i class="fa-solid fa-boxes-stacked me-2"></i> <?= $totalKomoditas ?> jenis komoditas terdaftar
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="k-card card-dark shadow-sm">
            <div style="font-size:0.7rem; font-weight:700; letter-spacing:1px; opacity:0.7; margin-bottom:16px;">TOTAL KOMODITAS</div>
            <div class="d-flex align-items-baseline mb-4">
                <span style="font-size:2.5rem; font-weight:800; line-height:1; letter-spacing:-1px;"><?= $totalKomoditas ?></span>
                <span class="ms-2" style="opacity:0.8;">Produk</span>
            </div>
            <div style="font-size:0.85rem; font-weight:700;">
                <i class="fa-solid fa-seedling me-2 text-success"></i> Database sayuran aktif
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="k-card card-beige shadow-sm">
            <div style="font-size:0.7rem; font-weight:700; letter-spacing:1px; opacity:0.7; margin-bottom:12px;">STOK TERBANYAK</div>
            <h3 class="fw-bold mb-4" style="color:#112a1f;"><?= esc($topProduct) ?></h3>
            <div class="d-flex align-items-center gap-3">
                <div class="progress flex-grow-1" style="height:6px; background:rgba(0,0,0,0.1);">
                    <div class="progress-bar" style="width:<?= $demandPct ?>%; background:#112a1f;"></div>
                </div>
                <div style="font-size:0.7rem; font-weight:800; line-height:1.2;"><?= $demandPct ?>%<br>Share</div>
            </div>
        </div>
    </div>
</div>

<div class="table-container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" style="color:#112a1f;">Status Inventaris Lapangan</h3>
        <span class="text-muted fw-bold" style="font-size:0.8rem;"><?= $totalKomoditas ?> komoditas</span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-borderless table-inv align-middle mb-0">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="25%">NAMA SAYUR</th>
                    <th width="15%">HARGA/KG</th>
                    <th width="15%">STOK (KG)</th>
                    <th width="15%">STATUS</th>
                    <th width="25%" class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($stocks)): ?>
                    <?php foreach($stocks as $index => $item): ?>
                    <tr>
                        <td class="fw-bold text-muted px-3"><?= sprintf('%02d', $index + 1) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($item['image_path']): ?>
                                    <img src="<?= base_url('images/'.$item['image_path']) ?>" class="plant-img" onerror="this.src='https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&q=80&w=100'">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&q=80&w=100" class="plant-img">
                                <?php endif; ?>
                                <div>
                                    <span class="fw-bold text-dark fs-6 d-block"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="text-muted" style="font-size:0.75rem; letter-spacing:1px;"><?= $item['sku'] ?> · <?= $item['category'] ?? '' ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold text-dark">Rp <?= number_format($item['price_per_kg'], 0, ',', '.') ?></td>
                        <td class="fs-5 text-dark font-monospace fw-bold" style="letter-spacing:1px;"><?= number_format($item['total_weight_kg'], 2) ?></td>
                        <td>
                            <?php if($item['status'] == 'ADA'): ?>
                                <span class="pill-green">Tersedia</span>
                            <?php elseif($item['status'] == 'RENDAH'): ?>
                                <span class="pill-yellow">Rendah</span>
                            <?php else: ?>
                                <span class="pill-red">KOSONG</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="action-btn border-0" title="Tambah Stok" data-bs-toggle="modal" data-bs-target="#addStockModal" onclick="document.getElementById('stock_id_select').value='<?= $item['id'] ?>'"><i class="fa-solid fa-plus"></i></button>
                                <button class="action-btn border-0 text-primary" title="Edit Produk" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['product_id'] ?>"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn border-0 text-danger" title="Hapus Produk" onclick="confirmDelete('<?= base_url('manager/delete_product/'.$item['product_id']) ?>', '<?= addslashes($item['name']) ?>')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa-solid fa-inbox fa-3x mb-3 d-block" style="opacity:0.3;"></i>Belum ada data stok komoditas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <div class="text-muted" style="font-size:0.75rem; font-weight:800; letter-spacing:1.5px;">MENAMPILKAN <?= count($stocks) ?> DARI <?= $totalKomoditas ?> KOMODITAS</div>
    </div>
</div>

<?php if(!empty($stocks)): ?>
<div class="row g-4 mb-5">
    <?php 
    $lowStockItems = array_filter($stocks, fn($s) => $s['status'] == 'KOSONG' || $s['status'] == 'RENDAH');
    $availableItems = array_filter($stocks, fn($s) => $s['status'] == 'ADA');
    ?>
    <div class="col-md-6">
        <div class="bottom-card">
            <div class="bc-icon"><i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i></div>
            <div>
                <h5 class="fw-bold mb-2">Stok Kritis</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <?php if(count($lowStockItems) > 0): ?>
                        Terdapat <strong><?= count($lowStockItems) ?></strong> komoditas dengan stok rendah/kosong yang perlu segera di-restock melalui panen berikutnya.
                    <?php else: ?>
                        Semua komoditas dalam kondisi stok aman. Tidak ada yang perlu di-restock segera.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="bottom-card">
            <div class="bc-icon"><i class="fa-solid fa-chart-simple"></i></div>
            <div>
                <h5 class="fw-bold mb-2">Ringkasan Gudang</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <strong><?= count($availableItems) ?></strong> komoditas tersedia, total bobot gudang <strong><?= number_format($totalStokKg, 0, ',', '.') ?> Kg</strong>. Komoditas terbanyak: <strong><?= esc($topProduct) ?></strong>.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Tambah Sayur Baru -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;">Registrasi Sayur Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('manager/create_product') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <p class="text-muted mb-4" style="font-size:0.9rem;">Daftarkan SKU produk/komoditas baru ke dalam database ekosistem Greenhouse.</p>
                    
                    <div class="mb-3">
                        <div class="p-3 mb-3 text-center" style="border: 2px dashed #d1d5db; border-radius: 12px; background: #fafafa;">
                            <label class="fw-bold text-muted mb-2 d-block" style="font-size:0.75rem; letter-spacing:1px;">UNGGAH FOTO PRODUK</label>
                            <input type="file" name="product_image" class="form-control border-0 bg-transparent" accept="image/*" required>
                            <small class="text-muted">Format: JPG/PNG, Max: 2MB</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NAMA SAYURAN</label>
                        <input type="text" class="form-control" name="name" required placeholder="Contoh: Cabai Gendot Merah" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">KATEGORI</label>
                            <select class="form-select" name="category" required style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                                <option value="Sayur Daun">Sayur Daun</option>
                                <option value="Sayur Buah">Sayur Buah</option>
                                <option value="Rempah/Bumbu">Rempah/Bumbu</option>
                                <option value="Umbi">Umbi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">KODE SKU</label>
                            <input type="text" class="form-control" name="sku" required placeholder="CBD-100" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600; text-transform:uppercase;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">HARGA PASAR (PER KG)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius:12px 0 0 12px; border:1px solid #dee2e6; color:#999; font-weight:700;">Rp</span>
                            <input type="number" class="form-control" name="price_per_kg" required placeholder="35000" style="border-radius:0 12px 12px 0; padding:12px; font-weight:bold; background:#f5f6f8;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-dark-green"><i class="fa-solid fa-seedling me-2"></i> Daftarkan Komoditas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Hasil Panen -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;">Manifes Input Panen Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('manager/add_stock') ?>" method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted mb-4" style="font-size:0.9rem;">Catat panen hari ini ke dalam gudang inventori. Nilai ini akan real-time tampil di e-Commerce Katalog pelanggan.</p>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">KOMODITAS (SKU & NAMA)</label>
                        <select class="form-select form-control" name="stock_id" id="stock_id_select" required style="border-radius:12px; padding:12px; font-weight:600; background:#f5f6f8;">
                            <option value="">-- Pilih Komoditas --</option>
                            <?php foreach($stocks as $st): ?>
                                <option value="<?= $st['id'] ?>">[<?= $st['sku'] ?>] <?= htmlspecialchars($st['name']) ?> (Tersedia: <?= $st['total_weight_kg'] ?> Kg)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">BERAT PANEN BARU (KG)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.1" max="<?= MAX_STOCK_PER_PRODUCT ?>" class="form-control" name="added_weight" required placeholder="Maks <?= MAX_STOCK_PER_PRODUCT ?> Kg" style="border-radius:12px 0 0 12px; padding:12px; font-size:1.1rem; font-weight:bold; background:#f5f6f8;">
                            <span class="input-group-text bg-white" style="border-radius:0 12px 12px 0; border:1px solid #dee2e6; color:#999; font-weight:700;">KG</span>
                        </div>
                    </div>

                    <!-- AKS 2.0: Estimasi Panen (ditampilkan saat stok habis) -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">ESTIMASI PANEN BERIKUTNYA (OPSIONAL)</label>
                        <input type="text" class="form-control" name="estimated_harvest" placeholder="Contoh: 15 Juli 2026 atau 2 minggu lagi" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                        <small class="text-muted mt-1 d-block"><i class="fa-solid fa-info-circle me-1"></i>Akan ditampilkan di katalog pelanggan jika stok habis.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px; font-weight:600;">Batalkan</button>
                    <button type="submit" class="btn btn-dark-green"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Sinkronisasi Stok Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk (1 per produk) -->
<?php if(!empty($stocks)): ?>
<?php foreach($stocks as $item): ?>
<div class="modal fade" id="editModal<?= $item['product_id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color:#112a1f;"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Produk: <?= esc($item['name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('manager/edit_product') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <div class="p-3 text-center" style="border: 2px dashed #d1d5db; border-radius: 12px; background: #fafafa;">
                            <?php if($item['image_path']): ?>
                                <img src="<?= base_url('images/'.$item['image_path']) ?>" style="width:80px; height:80px; border-radius:12px; object-fit:cover; margin-bottom:8px;" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <label class="fw-bold text-muted mb-2 d-block" style="font-size:0.75rem; letter-spacing:1px;">GANTI FOTO (opsional)</label>
                            <input type="file" name="product_image" class="form-control border-0 bg-transparent" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">NAMA SAYURAN</label>
                        <input type="text" class="form-control" name="name" required value="<?= esc($item['name']) ?>" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">KATEGORI</label>
                            <select class="form-select" name="category" required style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600;">
                                <option value="Sayur Daun" <?= ($item['category'] ?? '') == 'Sayur Daun' ? 'selected' : '' ?>>Sayur Daun</option>
                                <option value="Sayur Buah" <?= ($item['category'] ?? '') == 'Sayur Buah' ? 'selected' : '' ?>>Sayur Buah</option>
                                <option value="Rempah/Bumbu" <?= ($item['category'] ?? '') == 'Rempah/Bumbu' ? 'selected' : '' ?>>Rempah/Bumbu</option>
                                <option value="Umbi" <?= ($item['category'] ?? '') == 'Umbi' ? 'selected' : '' ?>>Umbi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">KODE SKU</label>
                            <input type="text" class="form-control" name="sku" required value="<?= esc($item['sku']) ?>" style="border-radius:12px; padding:12px; background:#f5f6f8; font-weight:600; text-transform:uppercase;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">HARGA PASAR (PER KG)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius:12px 0 0 12px; border:1px solid #dee2e6; color:#999; font-weight:700;">Rp</span>
                            <input type="number" class="form-control" name="price_per_kg" required value="<?= $item['price_per_kg'] ?>" style="border-radius:0 12px 12px 0; padding:12px; font-weight:bold; background:#f5f6f8;">
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
