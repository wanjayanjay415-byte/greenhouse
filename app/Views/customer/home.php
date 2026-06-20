<?= $this->extend('layout/customer') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --gh-green: #0b2e21;
        --gh-green-light: #1c4b37;
        --gh-emerald: #2e7d32;
        --gh-accent: #a3cfba;
        --gh-bg: #f5f8f6;
        --gh-card-bg: #ffffff;
    }
    
    body { background-color: var(--gh-bg); }

    /* Hero Section Premium */
    .hero-container {
        padding: 40px 30px;
    }
    
    .hero-section {
        position: relative;
        border-radius: 32px;
        overflow: hidden;
        min-height: 550px;
        display: flex;
        align-items: center;
        background: url('<?= base_url('images/nano_banana_hero.png') ?>') center/cover no-repeat;
        box-shadow: 0 25px 60px rgba(11, 46, 33, 0.12);
        animation: heroFadeIn 1s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @keyframes heroFadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(11, 46, 33, 0.95) 0%, rgba(11, 46, 33, 0.65) 50%, rgba(11, 46, 33, 0.2) 100%);
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        padding: 80px;
        max-width: 700px;
        color: #fff;
    }
    
    .hero-tag {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        color: #a3cfba;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 2px;
        padding: 8px 20px;
        border-radius: 50px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 25px;
    }
    
    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.8rem;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 25px;
        letter-spacing: -1px;
    }
    
    .hero-subtitle {
        font-size: 1.15rem;
        font-weight: 400;
        line-height: 1.7;
        color: #d1dfd9;
        margin-bottom: 40px;
    }
    
    .btn-hero-action {
        background: #ffffff;
        color: var(--gh-green);
        border: none;
        padding: 16px 36px;
        font-weight: 700;
        font-size: 0.95rem;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    .btn-hero-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        background: var(--gh-accent);
        color: var(--gh-green);
    }
    
    /* Features Section */
    .features-container {
        padding: 0 30px 60px 30px;
    }
    
    .feature-card {
        background: var(--gh-card-bg);
        border-radius: 24px;
        padding: 40px 35px;
        border: 1px solid rgba(0,0,0,0.02);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(11, 46, 33, 0.05);
        border-color: rgba(46, 125, 50, 0.1);
    }
    
    .feature-icon-wrapper {
        width: 65px;
        height: 65px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }
    
    .bg-feat-1 { background: rgba(46, 125, 50, 0.08); color: var(--gh-emerald); }
    .bg-feat-2 { background: rgba(11, 46, 33, 0.08); color: var(--gh-green); }
    .bg-feat-3 { background: rgba(163, 207, 186, 0.15); color: var(--gh-green-light); }
    
    .feature-card:hover .feature-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    
    .feature-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--gh-green);
        margin-bottom: 12px;
    }
    
    .feature-desc {
        color: #556c60;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 0;
    }
    
    /* Catalog Section */
    .catalog-section {
        padding: 20px 30px 80px 30px;
    }
    
    .section-header {
        margin-bottom: 45px;
    }
    
    .section-pretitle {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--gh-emerald);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.6rem;
        font-weight: 800;
        color: var(--gh-green);
        letter-spacing: -0.5px;
    }
    
    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 35px;
    }

    /* Filter Kategori Beranda */
    .category-filter { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin: 0 0 45px; }
    .cat-chip { padding: 10px 22px; border-radius: 50px; border: 1.5px solid rgba(11,46,33,0.15); background: #fff; color: #0b2e21; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.25s; }
    .cat-chip:hover { border-color: #0b2e21; }
    .cat-chip.active { background: #0b2e21; color: #fff; border-color: #0b2e21; }

    /* Kartu bisa diklik = pesan langsung */
    .product-card-modern.clickable { cursor: pointer; }
    .order-hint-modern { display: inline-flex; align-items: center; font-size: 0.78rem; font-weight: 700; color: #2e7d32; margin-top: 10px; }
    .product-card-modern.clickable:hover .order-hint-modern { text-decoration: underline; }
    
    /* Product Card Premium */
    .product-card-modern {
        background: var(--gh-card-bg);
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.015);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.02);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .product-card-modern:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(11, 46, 33, 0.08);
        border-color: rgba(46, 125, 50, 0.08);
    }
    
    .prod-img-container {
        position: relative;
        height: 280px;
        overflow: hidden;
    }
    
    .prod-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .product-card-modern:hover .prod-img-container img {
        transform: scale(1.06);
    }
    
    .stock-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
        background: rgba(255, 255, 255, 0.95);
        color: var(--gh-green);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(8px);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .stock-badge-out {
        background: rgba(239, 68, 68, 0.95);
        color: #fff;
    }
    
    .grade-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 800;
        z-index: 2;
        background: var(--gh-green);
        color: #fff;
        letter-spacing: 0.5px;
    }
    
    .prod-details {
        padding: 30px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .prod-category {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--gh-emerald);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    
    .prod-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--gh-green);
        margin-bottom: 20px;
        line-height: 1.3;
    }
    
    .prod-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #f2f5f3;
    }
    
    .price-box {
        display: flex;
        flex-direction: column;
    }
    
    .price-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gh-green);
        line-height: 1.1;
    }
    
    .price-lbl {
        font-size: 0.75rem;
        color: #728279;
        margin-top: 4px;
        font-weight: 500;
    }
    
    .btn-buy-instantly {
        background: var(--gh-green);
        color: #fff;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(11, 46, 33, 0.15);
    }
    
    .btn-buy-instantly:hover {
        background: var(--gh-emerald);
        transform: scale(1.08);
        box-shadow: 0 10px 25px rgba(46, 125, 50, 0.25);
    }
    .btn-buy-instantly:active {
        transform: scale(0.96);
    }
    
    /* Footer Style */
    .footer-premium {
        background: var(--gh-green);
        color: #c9d5cf;
        padding: 90px 40px 40px 40px;
        margin-top: 100px;
        border-radius: 48px 48px 0 0;
        box-shadow: 0 -15px 40px rgba(11, 46, 33, 0.05);
    }
    
    .footer-premium h5 {
        color: #fff;
        font-weight: 700;
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    
    .footer-link {
        color: #a3b8ad;
        text-decoration: none;
        transition: all 0.25s ease;
        display: block;
        margin-bottom: 12px;
        font-weight: 500;
        font-size: 0.95rem;
    }
    .footer-link:hover {
        color: #fff;
        padding-left: 4px;
    }
    
    .social-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        text-decoration: none;
    }
    .social-btn:hover {
        background: #fff;
        color: var(--gh-green);
        border-color: #fff;
        transform: translateY(-2px);
    }
</style>

<!-- Hero Container -->
<div class="hero-container">
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="hero-tag">🌱 Kualitas Eksotis Premium</span>
            <h1 class="hero-title">Panen Sayuran Segar Langsung Dari Greenhouse</h1>
            <p class="hero-subtitle">Kami mengadopsi teknologi hidroponik pintar presisi untuk menghasilkan sayuran segar berkualitas tinggi bebas pestisida. Dipanen segar dan langsung diantarkan.</p>
            <button class="btn-hero-action" onclick="document.getElementById('catalog').scrollIntoView({behavior: 'smooth'})">
                Mulai Belanja Sayur <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Features Container -->
<div class="features-container">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon-wrapper bg-feat-1">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h4 class="feature-title">Bebas Pestisida</h4>
                <p class="feature-desc">Fasilitas tertutup steril memastikan sayuran tumbuh alami tanpa kontaminasi bahan kimia berbahaya.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon-wrapper bg-feat-2">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <h4 class="feature-title">Teknologi Hidroponik</h4>
                <p class="feature-desc">Penggunaan nutrisi cair terstandarisasi untuk rasa yang lebih renyah, manis, dan kandungan gizi tinggi.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon-wrapper bg-feat-3">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h4 class="feature-title">Pengiriman di Hari Sama</h4>
                <p class="feature-desc">Kami memotong sayur hanya setelah pesanan Anda masuk untuk menjamin kesegaran optimal.</p>
            </div>
        </div>
    </div>
</div>

<!-- Catalog Container -->
<div class="catalog-section" id="catalog">
    <div class="section-header">
        <span class="section-pretitle">Pilihan Terbaik</span>
        <h2 class="section-title">Katalog Panen Hari Ini</h2>
        <p class="text-muted fs-6 mb-0">Stok sayuran real-time yang siap dipanen dan dikirim langsung dari perkebunan.</p>
    </div>

    <?php if(empty($stocks)): ?>
        <div class="text-center py-5 my-5 bg-white rounded-5 border border-light shadow-sm">
            <i class="fa-solid fa-box-open fs-1 text-muted mb-3" style="opacity:0.3;"></i>
            <h4 class="text-muted fw-bold">Katalog Kosong</h4>
            <p class="text-muted mb-0">Stok panen sedang diperbarui oleh pengelola Greenhouse.</p>
        </div>
    <?php else: ?>
        <?php
            // Kategori unik untuk filter
            $homeCategories = [];
            foreach ($stocks as $s) {
                $c = $s['category'] ?? 'Lainnya';
                if (!in_array($c, $homeCategories)) $homeCategories[] = $c;
            }
        ?>
        <div class="category-filter" id="categoryFilter">
            <button class="cat-chip active" data-cat="all" onclick="filterByCategory('all', this)">Semua</button>
            <?php foreach ($homeCategories as $c): ?>
                <button class="cat-chip" data-cat="<?= esc(strtolower($c)) ?>" onclick="filterByCategory('<?= esc(strtolower($c), 'js') ?>', this)"><?= esc($c) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="catalog-grid">
            <?php foreach($stocks as $st): ?>
                <?php $imgUrl = $st['image_path'] ? base_url('images/'.$st['image_path']) : 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&q=80&w=600'; ?>
                <?php $catSlug = strtolower($st['category'] ?? 'lainnya'); $inStock = ($st['status'] == 'ADA'); ?>

                <div class="product-card-modern <?= $inStock ? 'clickable' : '' ?>" data-cat="<?= esc($catSlug) ?>"
                     <?php if ($inStock): ?>onclick="orderNow('<?= esc($st['name'], 'js') ?>', <?= $st['price_per_kg'] ?>, '<?= $imgUrl ?>', <?= $st['total_weight_kg'] ?>)"<?php endif; ?>>
                    <div class="prod-img-container">
                        <?php if($st['status'] == 'ADA'): ?>
                            <span class="stock-badge"><i class="fa-solid fa-circle-check text-success"></i> Tersedia <?= $st['total_weight_kg'] ?> Kg</span>
                        <?php else: ?>
                            <span class="stock-badge stock-badge-out"><i class="fa-solid fa-circle-xmark"></i> Habis</span>
                        <?php endif; ?>
                        
                        <span class="grade-badge">Grade <?= $st['grade'] ?></span>
                        <img src="<?= $imgUrl ?>" alt="<?= esc($st['name']) ?>">
                    </div>
                    
                    <div class="prod-details">
                        <span class="prod-category"><?= htmlspecialchars($st['category'] ?? 'Sayuran Segar') ?></span>
                        <h3 class="prod-name"><?= htmlspecialchars($st['name']) ?></h3>
                        <?php if($inStock): ?>
                            <span class="order-hint-modern"><i class="fa-solid fa-bolt me-1"></i>Klik untuk pesan langsung</span>
                        <?php endif; ?>

                        <div class="prod-footer">
                            <div class="price-box">
                                <span class="price-val">Rp <?= number_format($st['price_per_kg'], 0, ',', '.') ?></span>
                                <span class="price-lbl">per kilogram</span>
                            </div>
                            
                            <?php if($st['status'] == 'ADA'): ?>
                                <button class="btn-buy-instantly" onclick="event.stopPropagation(); addToCart('<?= esc($st['name'], 'js') ?>', <?= $st['price_per_kg'] ?>, '<?= $imgUrl ?>', <?= $st['total_weight_kg'] ?>)" title="Tambah ke Keranjang">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn-buy-instantly bg-secondary opacity-50" disabled style="cursor:not-allowed; box-shadow:none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="footer-premium">
    <div class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-6">
                <h3 class="fw-bold text-white mb-3" style="font-family:'Playfair Display', serif; font-size: 2rem;">GreenHouse</h3>
                <p class="pe-lg-5 mb-4 text-muted" style="line-height: 1.8; font-size: 0.95rem;">Fasilitas perkebunan modern terintegrasi dengan teknologi mutakhir untuk mempersembahkan kualitas sayuran hidroponik premium terbaik bagi Anda sekeluarga.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4 text-uppercase fs-6" style="letter-spacing: 1px;">Kemitraan</h5>
                <a href="#" class="footer-link">Fasilitas Greenhouse</a>
                <a href="#" class="footer-link">Sistem Budidaya</a>
                <a href="#" class="footer-link">Kerjasama Agen</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4 text-uppercase fs-6" style="letter-spacing: 1px;">Bantuan</h5>
                <a href="<?= base_url('status') ?>" class="footer-link">Lacak Status Kiriman</a>
                <a href="#" class="footer-link">Faq & Bantuan</a>
                <a href="#" class="footer-link">Syarat & Ketentuan</a>
            </div>
        </div>
        <div class="border-top border-secondary mt-5 pt-4 text-center text-muted" style="font-size:0.85rem; border-color: rgba(255,255,255,0.08) !important;">
            &copy; 2026 GreenHouse Management System. All rights reserved.
        </div>
    </div>
</footer>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// PESAN LANGSUNG: klik kartu = tambah ke keranjang lalu menuju checkout
function orderNow(name, price, img, maxStock) {
    addToCart(name, price, img, maxStock); // addToCart sudah menangani cek login
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
        setTimeout(() => { window.location.href = '<?= base_url('checkout') ?>'; }, 350);
    }
}

// FILTER KATEGORI BERANDA
function filterByCategory(cat, btn) {
    document.querySelectorAll('#categoryFilter .cat-chip').forEach(c => c.classList.remove('active'));
    if (btn) btn.classList.add('active');

    document.querySelectorAll('.catalog-grid .product-card-modern').forEach(card => {
        const show = (cat === 'all') || (card.dataset.cat === cat);
        card.style.display = show ? '' : 'none';
    });
}
</script>
<?= $this->endSection() ?>
