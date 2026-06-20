<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Manajer | Arsip Laporan</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; overflow-x: hidden; margin:0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        
        .login-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 480px;
        }
        
        .sys-portal { font-size:2.4rem; font-weight:800; color:#112a1f; letter-spacing:-1px; margin-bottom:12px; font-family:'Inter', sans-serif;}
        .sys-subtitle { font-size:0.95rem; color:#666; margin-bottom:40px;}
        
        .form-label { font-size: 0.75rem; font-weight: 700; color: #555; letter-spacing: 1px; display:block; margin-bottom:10px; text-transform:uppercase;}
        .input-group-custom { position:relative; margin-bottom:30px;}
        .input-group-custom > i:first-child { position:absolute; left:20px; top:50%; transform:translateY(-50%); color:#888; font-size:1.1rem;}
        .form-control { border-radius: 12px; padding: 18px 20px 18px 50px; border: none; background: #eaecf0; font-size: 0.95rem; font-weight:600; color:#112a1f; transition: 0.2s;}
        .form-control::placeholder { color:#a0a5ad; font-weight:500;}
        .form-control:focus { background: #e0e3e8; box-shadow: none; outline:none; }
        
        .btn-init { background: #073a26; color: #fff; padding: 20px; border-radius: 12px; font-weight: 700; width: 100%; border: none; font-size: 0.85rem; letter-spacing:2px; text-transform:uppercase; transition: 0.2s; margin-top:10px;}
        .btn-init:hover { background: #0a4f34; }
        
        .bottom-terms { text-align:center; font-size:0.75rem; color:#888; margin-top:40px; line-height:1.6;}
        .bottom-terms a { color:#112a1f; font-weight:700; text-decoration:none; border-bottom:1px solid #112a1f;}

        .eye-icon { position:absolute; left:auto; right:20px; top:50%; transform:translateY(-50%); color:#bbb; cursor:pointer;}
    </style>
</head>
<body>

<div class="login-card">
    <h2 class="sys-portal">Portal Manajer</h2>
    <p class="sys-subtitle">Masukkan kredensial Anda untuk mengakses sistem.</p>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="font-size: 0.9rem; border-radius: 8px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/login_process') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>
        <div>
            <label class="form-label">EMAIL MANAJER</label>
            <div class="input-group-custom">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" class="form-control" required autocomplete="off" spellcheck="false" data-lpignore="true">
            </div>
        </div>
        
        <div>
            <div class="clearfix mb-2">
                <label class="form-label float-start m-0">KATA SANDI</label>
            </div>
            <div class="input-group-custom">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" id="password_input" class="form-control" required autocomplete="new-password" data-lpignore="true">
                <i class="fa-regular fa-eye eye-icon" onclick="togglePassword()"></i>
            </div>
        </div>

        <div class="form-check mb-4" style="margin-top:-15px;">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" style="cursor:pointer;">
            <label class="form-check-label text-muted fw-bold" style="font-size:0.8rem; cursor:pointer;" for="remember">Ingat Saya</label>
        </div>

        <button type="submit" class="btn-init">Masuk <i class="fa-solid fa-arrow-right ms-2"></i></button>
    </form>
                
    <div class="bottom-terms">
        Dengan mengakses GreenHouse, Anda menyetujui <a href="#">Syarat Penyimpanan</a> dan <a href="#">Kebijakan Privasi</a>.
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password_input');
        const icon = document.querySelector('.eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
