<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal Sistem' ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fa; margin: 0; display: flex; height: 100vh; overflow: hidden;}
        /* Left Split */
        .login-left {
            width: 45%;
            background: linear-gradient(135deg, rgba(8,38,26,0.95), rgba(16,60,42,0.9) 100%), url('https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&q=80&w=1000') center/cover no-repeat;
            color: #fff;
            padding: 80px 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .login-left .logo {
            font-size: 1.4rem; font-weight: 700; letter-spacing: 1px; display: flex; align-items: center; gap: 12px;
        }
        .login-left .logo-text span { font-weight: 400; font-size: 0.75rem; letter-spacing: 2px; display: block; opacity: 0.6;}
        
        .hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            line-height: 1.2;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .hero-text p { font-size: 1.1rem; color: #a3cfba; max-width: 450px; line-height: 1.6;}
        
        /* Right Split */
        .login-right {
            width: 55%;
            background: #fafbfc;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 480px;
        }
        .login-title { font-weight: 700; font-size: 2.2rem; color: #112a1f; margin-bottom: 8px;}
        .login-subtitle { color: #555; margin-bottom: 40px;}
        
        .form-label { font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; color: #555; display:flex; justify-content: space-between;}
        
        .input-group-custom {
            background: #eef2f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 4px 16px;
            margin-bottom: 24px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .input-group-custom:focus-within { border-color: #112a1f; background: #fff;}
        .input-group-custom i { color: #888; font-size: 1.1rem;}
        .input-group-custom input {
            border: none; background: transparent; padding: 12px 16px; width: 100%; outline: none; font-weight: 500; color: #333;
        }
        
        .btn-login {
            background-color: #0b2e21;
            color: #fff;
            width: 100%;
            padding: 16px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 0.95rem;
            transition: all 0.3s;
            border: none;
        }
        .btn-login:hover { background-color: #164734; }
        
        .secure-protocols { margin-top: 50px; }
        .secure-line {
            display: flex; align-items: center; text-align: center; color: #888; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 24px;
        }
        .secure-line::before, .secure-line::after { content: ''; flex: 1; border-bottom: 1px solid #ddd; }
        .secure-line span { padding: 0 16px; }
        
        .protocol-btn {
            background: #f0f4f2; color: #333; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 0.85rem; border: none; text-align: center; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;
        }
        .protocol-btn:hover { background: #e2e8e5; }
        
        .terms { margin-top: 60px; font-size: 0.75rem; text-align: center; color: #888;}
        .terms a { color: #555; font-weight: 600; text-decoration: none; border-bottom: 1px solid #555;}
    </style>
</head>
<body>

    <div class="login-left">
        <div class="logo">
            <i class="fa-solid fa-seedling fs-3"></i>
            <div class="logo-text">GreenHouse <span>DATA CENTER — V1.0</span></div>
        </div>
        
        <div class="hero-text">
            <p class="text-uppercase fw-bold text-light mb-4" style="font-size:0.75rem; letter-spacing:1px; border-bottom: 2px solid #a3cfba; display:inline-block; padding-bottom:8px;">Kecerdasan Agrikultur</p>
            <h1>Mengolah Presisi <br><span style="color:#a3cfba;">Melalui Data<br>Terarsip.</span></h1>
            <p>Akses Pusat Data Greenhouse untuk mengelola inventaris tanaman musiman, memantau log klimatologi, dan mengekspor arsip pelaporan secara aman.</p>
        </div>
        
        <div style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 600; opacity:0.8;">
            <i class="fa-solid fa-shield-halved me-2"></i> ENKRIPSI END-TO-END AKTIF
        </div>
    </div>
    
    <div class="login-right">
        <div class="login-box">
            <h2 class="login-title">Portal Sistem</h2>
            <p class="login-subtitle">Masukkan kredensial Anda untuk mengakses arsip pelaporan.</p>
            
            <form action="<?= base_url('manager') ?>" method="GET">
                <label class="form-label text-uppercase">ID Akun Pekerja</label>
                <div class="input-group-custom">
                    <i class="fa-regular fa-id-badge"></i>
                    <input type="text" placeholder="Contoh: ARCHIVE-8829" required>
                </div>
                
                <label class="form-label text-uppercase">
                    Kunci Akses (Sandi) 
                    <a href="#" class="text-dark text-decoration-none">RESET KUNCI</a>
                </label>
                <div class="input-group-custom">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" placeholder="••••••••••••" required>
                    <i class="fa-regular fa-eye cursor-pointer" onclick="alert('Toggle Password')"></i>
                </div>
                
                <button type="submit" class="btn-login mt-2">INISIALISASI AKSES <i class="fa-solid fa-arrow-right ms-2"></i></button>
            </form>
            
            <div class="secure-protocols">
                <div class="secure-line"><span>PROTOKOL AMAN</span></div>
                <div class="row g-3">
                    <div class="col-6">
                        <button type="button" class="protocol-btn" onclick="alert('Memindai Sidik Jari/FaceID...')"><i class="fa-solid fa-fingerprint fs-5"></i> BIOMETRIK</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="protocol-btn" onclick="alert('Memuat Kamera Scanner...')"><i class="fa-solid fa-qrcode fs-5"></i> LOGIN QR</button>
                    </div>
                </div>
            </div>
            
            <div class="terms">
                Dengan mengakses GreenHouse, Anda menyetujui seluruh<br> 
                <a href="#">Syarat Pengawetan Data</a> dan <a href="#">Kebijakan Privasi</a>.
            </div>
        </div>
    </div>

</body>
</html>
