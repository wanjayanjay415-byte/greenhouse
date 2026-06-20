<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GreenHouse' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Hotwire Turbo for Instant Page Transitions -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9f8; color: #1a2a22; }
        h1, h2, h3, h4, h5, h6, .brand-logo { font-family: 'Playfair Display', serif; font-weight: 700; }
        
        /* Sticky Glassmorphism Navbar */
        .navbar-custom {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(11, 46, 33, 0.06);
            padding: 18px 0;
            transition: all 0.3s ease;
        }
        
        .brand-logo {
            font-size: 1.6rem;
            color: #0b2e21;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-logo span {
            color: #2e7d32;
        }

        .nav-link-custom {
            color: #4a5c53;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.25s ease;
            position: relative;
            text-decoration: none;
        }
        .nav-link-custom:hover {
            color: #0b2e21;
        }
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2.5px;
            background: #0b2e21;
            transition: all 0.25s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }
        .nav-link-custom:hover::after,
        .nav-link-custom.active::after {
            width: 60%;
        }
        .nav-link-custom.active {
            color: #0b2e21;
        }

        .btn-premium-login {
            background-color: #0b2e21;
            color: #fff;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(11, 46, 33, 0.15);
        }
        .btn-premium-login:hover {
            background-color: #0d3828;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(11, 46, 33, 0.2);
        }
        
        .avatar-circle {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0b2e21, #2e7d32);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .avatar-circle:hover {
            transform: scale(1.05);
        }

        .footer-custom { background-color: #f7f7f7; padding: 60px 0 20px 0; border-top: 1px solid #eaeaea; }

        /* Cart bounce */
        @keyframes cartBounce { 0%{transform:scale(1)} 50%{transform:scale(1.5)} 100%{transform:scale(1)} }
        .cart-bounce { animation: cartBounce 0.4s ease; }

        /* Cart item enter animation */
        @keyframes slideIn { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)} }
        .cart-item-anim { animation: slideIn 0.3s ease; }

        /* Fly to cart animation */
        .fly-item { position:fixed; z-index:9999; pointer-events:none; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.3); transition: all 0.6s cubic-bezier(0.2,1,0.3,1); }
    </style>
</head>
<body>

    <!-- Modern Glassmorphism Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container px-4">
            <a class="brand-logo" href="<?= base_url('/') ?>">
                <i class="fa-solid fa-leaf text-success"></i> Green<span>House</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="fa-solid fa-bars" style="color: #0b2e21; font-size: 1.2rem;"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="nav-link-custom <?= current_url() == base_url('/') ? 'active' : '' ?>" href="<?= base_url('/') ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom <?= str_contains(current_url(), 'checkout') ? 'active' : '' ?>" href="<?= base_url('checkout') ?>" onclick="return checkLoginBeforeAccess(event, 'Checkout')">Checkout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom <?= str_contains(current_url(), 'status') ? 'active' : '' ?>" href="<?= base_url('status') ?>" onclick="return checkLoginBeforeAccess(event, 'Status Pesanan')">Status Pesanan</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <!-- Cart Icon with Badge -->
                    <?php if(session()->get('isLoggedIn') && session()->get('role') == 'customer'): ?>
                    <a href="<?= base_url('checkout') ?>" class="position-relative text-decoration-none" id="cartNavIcon" style="color:#0b2e21;">
                        <i class="fa-solid fa-cart-shopping" style="font-size:1.25rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge" style="font-size:0.65rem; display:none;">0</span>
                    </a>
                    <?php endif; ?>

                    <!-- Profile / Login -->
                    <?php if(session()->get('isLoggedIn') && session()->get('role') == 'customer'): ?>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0 text-decoration-none dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle">
                                    <?= strtoupper(substr(session()->get('full_name'), 0, 1)) ?>
                                </div>
                                <span class="fw-bold d-none d-lg-inline" style="font-size:0.9rem; color:#0b2e21;"><?= esc(session()->get('full_name')) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 p-2" style="border-radius:18px; min-width:260px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">
                                <li class="px-3 py-3 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                            <?= strtoupper(substr(session()->get('full_name'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:0.95rem;"><?= esc(session()->get('full_name')) ?></div>
                                            <div class="text-muted" style="font-size:0.75rem;"><?= esc(session()->get('email')) ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li><a class="dropdown-item py-2.5 mt-2 rounded-3 text-dark fw-medium" href="<?= base_url('profile') ?>" style="font-size:0.85rem;"><i class="fa-solid fa-user-gear me-2 text-muted"></i> Profil & Keamanan</a></li>
                                <li><a class="dropdown-item py-2.5 rounded-3 text-dark fw-medium" href="<?= base_url('status') ?>" style="font-size:0.85rem;"><i class="fa-solid fa-truck me-2 text-muted"></i> Lacak Pesanan</a></li>
                                <li><hr class="dropdown-divider border-light"></li>
                                <li><a class="dropdown-item py-2.5 rounded-3 text-danger fw-bold" href="<?= base_url('auth/logout') ?>" style="font-size:0.85rem;"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('auth') ?>" class="btn btn-premium-login">
                            <i class="fa-solid fa-right-to-bracket me-1.5"></i> Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>


    <!-- Bootstrap JS + jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const isLoggedIn = <?= (session()->get('isLoggedIn') && session()->get('role') == 'customer') ? 'true' : 'false' ?>;

    function checkLoginBeforeAccess(event, pageName) {
        if (!isLoggedIn) {
            event.preventDefault();
            Swal.fire({
                title: 'Perlu Masuk Akun',
                text: `Untuk mengakses halaman ${pageName}, silakan masuk atau mendaftar terlebih dahulu.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0b2e21',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Masuk Sekarang',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-5 shadow-lg border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('auth') ?>";
                }
            });
            return false;
        }
        return true;
    }

    // ====== SHOPPING CART SYSTEM (Multi-Item) ======
    function addToCart(name, price, img, maxStock) {
        if (!isLoggedIn) {
            Swal.fire({
                title: 'Perlu Masuk Akun',
                text: 'Silakan masuk terlebih dahulu untuk melakukan pemesanan sayur segar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0b2e21',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Masuk Sekarang',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-5 shadow-lg border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('auth') ?>";
                }
            });
            return;
        }

        let cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
        const existing = cart.findIndex(c => c.name === name);

        if (existing >= 0) {
            if (cart[existing].qty >= maxStock) {
                Swal.fire({toast:true, position:'top-end', icon:'warning', title:'Batas stok tercapai! Maks ' + maxStock + ' Kg', showConfirmButton:false, timer:2000});
                return;
            }
            cart[existing].qty++;
            cart[existing].maxStock = maxStock;
        } else {
            cart.push({ name, price, img, maxStock, qty: 1 });
        }

        localStorage.setItem('gh_cart', JSON.stringify(cart));
        updateCartUI();

        // Animasi badge bounce
        const badge = document.getElementById('cartBadge');
        if (badge) {
            badge.classList.remove('cart-bounce');
            void badge.offsetWidth;
            badge.classList.add('cart-bounce');
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: name + ' ditambahkan ke keranjang!',
            showConfirmButton: false,
            timer: 1500
        });
    }

    // Update Cart Badge UI
    function updateCartUI() {
        const cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
        const badge = document.getElementById('cartBadge');
        if (badge) {
            const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Initialize cart badge on page load
    document.addEventListener('DOMContentLoaded', updateCartUI);
    document.addEventListener('turbo:load', updateCartUI);
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>

