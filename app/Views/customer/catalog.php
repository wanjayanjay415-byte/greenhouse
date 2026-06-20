<?= $this->extend('layout/customer') ?>
<?= $this->section('content') ?>

<style>
    .sidebar-filter { padding-right: 40px; }
    .filter-title { font-size: 0.9rem; letter-spacing: 1px; color: #555; font-weight: 700; text-transform: uppercase; margin-bottom: 20px; }
    .custom-checkbox { display: flex; align-items: center; margin-bottom: 12px; cursor: pointer;}
    .custom-checkbox input { width: 20px; height: 20px; accent-color: #0b2e21; margin-right: 12px; cursor:pointer;}
    .custom-checkbox span { flex-grow: 1; font-weight: 500; font-size: 0.95rem; }
    
    .tip-box { background: #f7f9fa; border-radius: 12px; padding: 24px; margin-top: 40px; }
    
    .product-card { border-radius: 16px; overflow: hidden; background: #fff; position: relative; transition: transform 0.3s; border: 1px solid #eaeaea; height: 100%; display: flex; flex-direction: column; cursor: pointer; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .product-img { width: 100%; height: 260px; object-fit: cover; }
    .product-info { padding: 24px; flex-grow: 1; display: flex; flex-direction: column; }
    .product-info h5 { font-weight: 600; margin-bottom: 5px; font-size: 1.15rem; }
    .product-price { font-weight: 600; font-size: 1.1rem; color: #112a1f; }
    .badge-custom { position: absolute; top: 15px; left: 15px; background: #eef2f0; color: #555; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;}
    
    .btn-add-cart { background-color: #0b2e21; color: #fff; border-radius: 8px; padding: 12px; font-weight: 600; transition: all 0.3s; border: none; width: 100%; }
    .btn-add-cart:hover { background-color: #1a4a35; color:#fff; }

    /* Aksi kartu: klik kartu = pesan langsung, tombol kecil = tambah keranjang */
    .card-actions { display: flex; align-items: center; gap: 10px; margin-top: auto; }
    .order-hint { flex-grow: 1; display: inline-flex; align-items: center; font-weight: 700; font-size: 0.85rem; color: #0b2e21; }
    .btn-add-cart-sm { flex-shrink: 0; width: 44px; height: 44px; border-radius: 10px; border: 1px solid #0b2e21; background: #fff; color: #0b2e21; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; cursor: pointer; }
    .btn-add-cart-sm:hover { background: #0b2e21; color: #fff; transform: scale(1.05); }
    .product-card:hover .order-hint { text-decoration: underline; }

    .ad-banner { background: linear-gradient(45deg, #112a1f, #2e7d32); border-radius: 16px; color: #fff; padding: 30px 20px; text-align: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(46,125,50,0.2); position:relative; overflow:hidden;}
    .ad-banner::before { content: ''; position: absolute; top:-50px; right:-50px; width:100px; height:100px; background:rgba(255,255,255,0.1); border-radius:50%; }
    .ad-img { width: 100%; border-radius: 12px; margin-bottom: 15px; }
    .ad-title { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.5rem; margin-bottom: 10px; }
    .ad-button { display: inline-block; background: #cddc39; color: #112a1f; font-weight: 700; padding: 10px 20px; border-radius: 20px; text-decoration: none; margin-top: 15px; font-size: 0.85rem;}
    
    /* AKS 2.0: Out of Stock greyed visual */
    .product-card.out-of-stock .product-img { filter: grayscale(100%); opacity: 0.5; }
    .harvest-badge { background: #fef9c3; color: #854d0e; font-size: 0.8rem; font-weight: 700; padding: 8px 14px; border-radius: 8px; margin-bottom:10px; display:inline-block; }
    .capacity-bar-container { background: #f7f9fa; border-radius: 16px; padding: 20px 24px; margin-bottom: 30px; border: 1px solid #eaeaea; }
</style>

<?php
// Fallback images by category
$defaultImages = [
    'Sayur Daun' => 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&q=80&w=600',
    'Rempah'     => 'https://images.unsplash.com/photo-1547842340-9a2cdd7471fb?auto=format&fit=crop&q=80&w=600',
    'Buah'       => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&q=80&w=600',
];
$defaultImg = 'https://images.unsplash.com/photo-1590868309235-ea34bed7bd7f?auto=format&fit=crop&q=80&w=600';

// Get unique categories for filter
$categories = [];
foreach ($products as $p) {
    $cat = $p['category'] ?? 'Lainnya';
    if (!in_array($cat, $categories)) $categories[] = $cat;
}
?>

<div class="container-fluid px-5 py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-2 sidebar-filter border-end">
            <h4 class="mb-5" style="font-family:'Playfair Display', serif;">Filter Katalog</h4>
            
            <div class="filter-title">Kategori</div>
            <?php foreach ($categories as $cat): ?>
            <label class="custom-checkbox">
                <input type="checkbox" class="filter-cat" data-cat="<?= esc(strtolower($cat)) ?>" checked>
                <span><?= esc($cat) ?></span>
            </label>
            <?php endforeach; ?>
            
            <div class="filter-title mt-5">Rentang Harga (per Kg)</div>
            <div class="mb-2">
                <input type="range" class="form-range" min="0" max="150000" value="150000" id="priceRange" style="accent-color:#0b2e21;">
            </div>
            <div class="d-flex justify-content-between mb-4 pb-2" style="font-size:0.85rem; font-weight:600;">
                <span>Rp 0</span>
                <span id="priceLabel">Rp 150.000+</span>
            </div>
            
            <div class="tip-box">
                <h6 class="fw-bold mb-2 text-dark">Tips Petani <i class="fa-solid fa-lightbulb ms-1 text-warning"></i></h6>
                <p class="text-muted mb-0" style="font-size:0.85rem;">Sayuran daun hijau lebih baik dipanen pagi hari untuk menjaga kerenyahan dan kadar airnya.</p>
            </div>
        </div>
        
        <!-- Main Catalog -->
        <div class="col-lg-7 px-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="display-6 fw-bold text-dark" style="font-family:'Playfair Display', serif;">Katalog Hasil Panen Segar</h1>
                    <p class="text-muted fs-5">Dikurasi langsung dari ekosistem hidroponik kami.</p>
                </div>
                <div>
                    <select class="form-select border-0 bg-light fw-semibold text-muted shadow-sm rounded-pill px-4" id="sortSelect">
                        <option value="popular">Urutkan: Stok Terbanyak</option>
                        <option value="low">Urutkan: Harga Terendah</option>
                        <option value="high">Urutkan: Harga Tertinggi</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-4 mb-5 pb-5" id="productGrid">
                <!-- Kapasitas Harian Kurir (AKS 2.0) -->
                <div class="col-12">
                    <div class="capacity-bar-container">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="font-size:0.85rem; color:#112a1f;"><i class="fa-solid fa-truck me-2"></i>Kapasitas Pengiriman Hari Ini</span>
                            <span class="fw-bold" style="font-size:0.85rem; color:<?= $todayKg >= 30 ? '#dc2626' : ($todayKg >= 20 ? '#d97706' : '#16a34a') ?>;"><?= number_format($todayKg, 1) ?> / <?= $maxDailyKg ?> Kg</span>
                        </div>
                        <div class="progress" style="height:10px; border-radius:8px;">
                            <?php $pct = min(100, ($todayKg / $maxDailyKg) * 100); ?>
                            <div class="progress-bar" style="width:<?= $pct ?>%; background:<?= $todayKg >= 30 ? '#dc2626' : ($todayKg >= 20 ? '#d97706' : '#16a34a') ?>;" role="progressbar"></div>
                        </div>
                        <?php if ($todayKg >= 30): ?>
                            <div class="mt-2 text-danger fw-bold" style="font-size:0.8rem;"><i class="fa-solid fa-ban me-1"></i>Kuota penuh! Pesanan baru dijadwalkan untuk besok.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $idx => $p): ?>
                        <?php 
                            // Sinkron dengan beranda: pakai gambar produk asli (image_path), fallback ke gambar kategori
                            $img = !empty($p['image_path']) ? base_url('images/' . $p['image_path']) : ($defaultImages[$p['category'] ?? ''] ?? $defaultImg);
                            $imgThumb = str_replace('w=600', 'w=200', $img);
                            $pricePerKg = (float)$p['price_per_kg'];
                            $stockKg = min((float)$p['total_weight_kg'], MAX_STOCK_PER_PRODUCT);
                            $stockColor = $p['status'] == 'KOSONG' ? '#dc2626' : ($p['status'] == 'RENDAH' ? '#d97706' : '#2e7d32');
                            $stockText = $p['status'] == 'KOSONG' ? 'Habis' : ($p['status'] == 'RENDAH' ? 'Stok Menipis' : number_format($stockKg, 0) . ' Kg tersisa');
                            $cat = strtolower($p['category'] ?? 'lainnya');
                        ?>
                        <?php
                            $isOutOfStock = ($p['status'] == 'KOSONG');
                            $harvestEstimate = $p['estimated_harvest'] ?? null;
                        ?>
                        <div class="col-md-6 mb-2 product-item" data-cat="<?= esc($cat) ?>" data-price="<?= $pricePerKg ?>" data-stock="<?= $stockKg ?>">
                            <div class="product-card <?= $isOutOfStock ? 'out-of-stock' : '' ?>"
                                 <?php if (!$isOutOfStock): ?>onclick="orderNow('<?= esc($p['name'], 'js') ?>', <?= $pricePerKg ?>, '<?= $imgThumb ?>', <?= min($stockKg, MAX_STOCK_PER_PRODUCT) ?>)" style="cursor:pointer;"<?php else: ?>style="cursor:default;"<?php endif; ?>>
                                <?php if ($idx == 0 && !$isOutOfStock): ?><span class="badge-custom">TERPOPULER</span><?php endif; ?>
                                <?php if ($isOutOfStock): ?><span class="badge-custom" style="background:#fef2f2; color:#dc2626;">HABIS</span><?php endif; ?>
                                <img src="<?= $img ?>" class="product-img" alt="<?= esc($p['name']) ?>">
                                <div class="product-info">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5><?= esc($p['name']) ?></h5>
                                        <span class="product-price">Rp <?= number_format($pricePerKg, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="text-muted d-flex align-items-center mb-2" style="font-size:0.85rem;">
                                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:<?= $stockColor ?>; margin-right:6px;"></span>
                                        <?= $stockText ?> · Grade <?= esc($p['grade']) ?>
                                    </div>
                                    <?php if ($isOutOfStock && $harvestEstimate): ?>
                                        <div class="harvest-badge"><i class="fa-solid fa-seedling me-1"></i>Estimasi Panen: <?= esc($harvestEstimate) ?></div>
                                    <?php endif; ?>
                                    <div class="text-muted mb-4" style="font-size:0.8rem;">
                                        <span class="badge bg-light text-dark border"><?= esc($p['category'] ?? 'Umum') ?></span>
                                        <span class="badge bg-light text-dark border">SKU: <?= esc($p['sku']) ?></span>
                                    </div>
                                    <?php if (!$isOutOfStock): ?>
                                     <div class="card-actions">
                                         <span class="order-hint"><i class="fa-solid fa-bolt me-2"></i>Klik kartu untuk pesan langsung</span>
                                         <button class="btn-add-cart-sm" title="Tambah ke keranjang"
                                             onclick="event.stopPropagation(); addToCart('<?= esc($p['name'], 'js') ?>', <?= $pricePerKg ?>, '<?= $imgThumb ?>', <?= min($stockKg, MAX_STOCK_PER_PRODUCT) ?>)">
                                             <i class="fa-solid fa-cart-plus"></i>
                                         </button>
                                     </div>
                                    <?php else: ?>
                                    <button class="btn-add-cart mt-auto" disabled style="background:#ccc; cursor:not-allowed;">
                                        <i class="fa-solid fa-ban me-2"></i>Stok Habis
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-seedling fa-3x mb-3 opacity-25"></i>
                        <p class="text-muted fw-bold">Belum ada produk tersedia di katalog.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Ads -->
        <div class="col-lg-3">
            <h5 class="fw-bold text-muted mb-4" style="font-size:0.9rem; letter-spacing:1px; text-transform:uppercase;">Eksklusif Hari Ini</h5>
            
            <div class="ad-banner">
                <h3 class="ad-title">Pekan Panen Raya!</h3>
                <p style="font-size:0.9rem; opacity:0.9;">Gunakan kode promo <strong>GREEN20</strong> untuk diskon 20% sayuran hijau.</p>
                <img src="https://images.unsplash.com/photo-1556886470-349f42fc26e9?auto=format&fit=crop&q=80&w=400" class="ad-img mt-3 shadow-sm border border-2 border-white" alt="Promo">
                <a href="#" class="ad-button" onclick="navigator.clipboard.writeText('GREEN20'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'Kode GREEN20 disalin!',showConfirmButton:false,timer:1500}); return false;">Salin Kode</a>
            </div>

            <div class="ad-banner" style="background: linear-gradient(45deg, #1e3a8a, #3b82f6);">
                <i class="fa-solid fa-droplet fs-1 mb-3 text-info"></i>
                <h3 class="ad-title">Nutrisi Hidroponik</h3>
                <p style="font-size:0.9rem; opacity:0.9;">Tingkatkan hasil panen Anda sendiri di rumah.</p>
                <a href="#" class="ad-button shadow-sm" style="background:#fff; color:#1e3a8a;">Beli Rp 45.000</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// PESAN LANGSUNG: klik kartu = tambah ke keranjang lalu menuju checkout
function orderNow(name, price, img, maxStock) {
    // addToCart sudah menangani cek login (menampilkan modal jika belum masuk)
    addToCart(name, price, img, maxStock);
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
        setTimeout(() => { window.location.href = '<?= base_url('checkout') ?>'; }, 350);
    }
}

// FILTER SYSTEM
function applyFilters() {
    const checkedCats = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.dataset.cat);
    const maxPrice = parseInt(document.getElementById('priceRange').value);
    document.getElementById('priceLabel').textContent = maxPrice >= 150000 ? 'Rp 150.000+' : 'Rp ' + maxPrice.toLocaleString('id-ID');

    document.querySelectorAll('.product-item').forEach(item => {
        const cat = item.dataset.cat;
        const price = parseInt(item.dataset.price);
        const catMatch = checkedCats.includes(cat);
        const priceMatch = price <= maxPrice;
        item.style.display = (catMatch && priceMatch) ? '' : 'none';
    });
}

document.querySelectorAll('.filter-cat').forEach(c => c.addEventListener('change', applyFilters));
document.getElementById('priceRange').addEventListener('input', applyFilters);

// SORT SYSTEM
document.getElementById('sortSelect').addEventListener('change', function() {
    const grid = document.getElementById('productGrid');
    const items = [...grid.querySelectorAll('.product-item')];
    items.sort((a, b) => {
        if (this.value === 'low') return a.dataset.price - b.dataset.price;
        if (this.value === 'high') return b.dataset.price - a.dataset.price;
        return b.dataset.stock - a.dataset.stock; // popular = most stock
    });
    items.forEach(i => grid.appendChild(i));
});
</script>
<?= $this->endSection() ?>
