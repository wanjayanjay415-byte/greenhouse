<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshGrow | Portal Pembeli</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6fcfa; overflow-x: hidden; margin:0; }
        .split-layout { display: flex; min-height: 100vh; }
        
        .left-panel {
            width: 45%;
            background: url('https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80') center/cover no-repeat;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px;
            color: #fff;
        }
        .left-panel::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.9) 0%, rgba(27, 94, 32, 0.7) 100%);
        }
        .left-content { position: relative; z-index: 2; }
        
        .right-panel {
            width: 55%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 40px;
        }
        
        .form-container { width: 100%; max-width: 420px; }
        
        .nav-tabs { border-bottom: none; gap: 20px; margin-bottom: 30px; }
        .nav-tabs .nav-link { 
            border: none; color: #888; font-weight: 700; font-size: 1.1rem; padding: 0 0 8px 0; background: transparent; transition: 0.3s;
        }
        .nav-tabs .nav-link.active { color: #2e7d32; border-bottom: 2px solid #2e7d32; }
        .nav-tabs .nav-link:hover:not(.active) { color: #2e7d32; }
        
        .form-label { font-size: 0.85rem; font-weight: 700; color: #444; letter-spacing: 0.5px; }
        .form-control { border-radius: 12px; padding: 14px 16px; border: 1px solid #eaeaea; background: #fafafa; font-size: 0.95rem; transition: 0.2s;}
        .form-control:focus { background: #fff; border-color: #2e7d32; box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1); }
        
        .btn-green { background: #2e7d32; color: #fff; padding: 15px; border-radius: 12px; font-weight: 700; width: 100%; border: none; font-size: 1rem; transition: 0.2s;}
        .btn-green:hover { background: #1b5e20; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(46,125,50,0.2);}
        
        .alert-msg { border-radius:12px; padding:14px; font-weight:600; font-size:0.9rem; margin-bottom:20px; }

        @media (max-width: 991px) {
            .split-layout { flex-direction: column; }
            .left-panel { width: 100%; height: 200px; padding: 30px; }
            .right-panel { width: 100%; padding: 40px 20px; }
            .left-content p, .left-content .brands { display: none; }
        }
    </style>
</head>
<body>

<div class="split-layout">
    <div class="left-panel">
        <div class="left-content">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-leaf me-2"></i> FreshGrow</h2>
        </div>
        <div class="left-content mt-auto">
            <h1 class="fw-bold display-5 mb-3" style="line-height:1.2;">Bergabunglah dengan<br>revolusi pangan organik.</h1>
            <p class="fs-6 opacity-75 mb-0" style="max-width:400px; line-height:1.6;">Dapatkan akses langsung ke sayuran segar berkualitas dari greenhouse langsung ke dapur Anda.</p>
        </div>
    </div>
    
    <div class="right-panel">
        <div class="form-container">
            <h3 class="fw-bold mb-4 text-dark">Selamat Datang 👋</h3>
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert-msg bg-danger-subtle text-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert-msg bg-success-subtle text-success"><i class="fa-solid fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <ul class="nav nav-tabs" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#signin" type="button">Sign In</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#signup" type="button">Sign Up</button>
                </li>
            </ul>
            
            <div class="tab-content" id="authContent">
                <!-- Sign In Form -->
                <div class="tab-pane fade show active" id="signin" role="tabpanel">
                    <form action="<?= base_url('auth/login_process') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="form-label">EMAIL ADDRESS</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" autocomplete="off" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label text-muted" style="font-size:0.85rem;" for="remember">Ingat Saya</label>
                            </div>
                            <a href="#" class="text-decoration-none text-success" style="font-size:0.85rem; font-weight:600;">Lupa Password?</a>
                        </div>
                        <button type="submit" class="btn-green">Masuk ke Katalog</button>
                    </form>
                </div>
                
                <!-- Sign Up Form -->
                <div class="tab-pane fade" id="signup" role="tabpanel">
                    <form action="<?= base_url('auth/register_process') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">NAMA LENGKAP</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Nama Lengkap Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">EMAIL ADDRESS</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NO. TELEPON</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0812-XXXX-XXXX" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">BUAT PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn-green">Daftar Akun Baru</button>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <span class="text-muted" style="font-size:0.8rem;">Staf pengelola greenhouse? <a href="<?= base_url('auth/manager') ?>" class="text-success text-decoration-none fw-bold">Login Disini</a></span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
