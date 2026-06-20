<?= $this->extend('layout/customer') ?>
<?= $this->section('content') ?>

<style>
    body { background-color: #f5f7f8; }
    .box-container { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%;}
    .main-title { font-size: 2.8rem; font-family: 'Playfair Display', serif; color: #112a1f; letter-spacing: -1px;}
    
    .form-control-custom { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; width:100%; outline:none;}
    .form-control-custom:focus { border-color: #0b2e21; box-shadow: 0 0 0 4px rgba(11,46,33,0.1);}
    .form-label { font-size:0.75rem; font-weight:700; letter-spacing:1px; color:#777;text-transform:uppercase;margin-bottom:8px;}
    
    .summary-box { background: #0b2e21; color:#fff; border-radius:16px; padding: 40px; margin-top:20px;}
    .summary-row { display:flex; justify-content:space-between; margin-bottom: 16px; font-weight:500;}
    .summary-row.total { font-size: 2rem; font-weight:700; border-top: 1px solid rgba(255,255,255,0.2); padding-top:24px; margin-top:16px;}
    
    .btn-place-order { background: #a3cfba; color: #0b2e21; font-weight: 700; font-size:1.1rem; padding: 18px; border-radius: 8px; width: 100%; border:none; transition:0.3s;}
    .btn-place-order:hover { background: #8bc2a7; transform: translateY(-2px);}

    .cod-badge { background: #eef7f2; border: 1px solid #a3cfba; color: #0b2e21; padding: 16px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
</style>

<div class="container-fluid px-5 py-5 pb-5 mb-5">
    
    <div class="mb-5">
        <h1 class="main-title">Penyelesaian Pesanan</h1>
        <p class="text-muted fs-5">Tinjauan akhir dan detail pengiriman untuk produk pilihan Anda.</p>
    </div>

    <div class="row g-5">
        <!-- Left Side: Items -->
        <div class="col-lg-7">
            <div class="box-container border">
                <h4 class="mb-4 fw-bold">Item Belanja Anda</h4>
                <div id="checkoutItems">
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-cart-shopping fa-3x mb-3 opacity-25"></i>
                        <p>Keranjang kosong. <a href="<?= base_url('catalog') ?>">Tambah Produk</a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Delivery Details & COD -->
        <div class="col-lg-5">
            <div class="box-container" style="background:#f0f3f2; box-shadow:none;">
                <h4 class="mb-4 fw-bold">Detail Pengiriman</h4>
                
                <?php if(session()->get('isLoggedIn')): ?>
                    <label class="form-label">NAMA LENGKAP</label>
                    <input type="text" class="form-control-custom" value="<?= esc(session()->get('full_name')) ?>" readonly>
                    
                    <label class="form-label">EMAIL NOTIFIKASI</label>
                    <input type="email" class="form-control-custom" value="<?= esc(session()->get('email')) ?>" readonly>
                <?php else: ?>
                    <label class="form-label">NAMA LENGKAP</label>
                    <input type="text" class="form-control-custom" placeholder="Nama Lengkap Anda">
                    
                    <label class="form-label">EMAIL NOTIFIKASI</label>
                    <input type="email" class="form-control-custom" placeholder="email@domain.com">
                <?php endif; ?>
                
                <label class="form-label">ALAMAT PENGIRIMAN</label>
                <textarea class="form-control-custom pb-3" id="deliveryAddress" placeholder="Contoh: Jl. Sudirman No 123, RT/RW, Kecamatan, Kota, Kodepos"><?= esc(session()->get('address') ?? '') ?></textarea>

                <!-- AKS 2.0: Kapasitas Harian -->
                <?php if ($todayKg >= 20): ?>
                <div class="alert <?= $todayKg >= 30 ? 'alert-danger' : 'alert-warning' ?> mb-4" style="border-radius:12px;">
                    <i class="fa-solid fa-<?= $todayKg >= 30 ? 'ban' : 'triangle-exclamation' ?> me-2"></i>
                    <?php if ($todayKg >= 30): ?>
                        <strong>Kapasitas pengiriman hari ini penuh!</strong> Pesanan akan dijadwalkan ke hari berikutnya.
                    <?php else: ?>
                        Sisa kuota pengiriman: <strong><?= number_format(30 - $todayKg, 1) ?> Kg</strong> (<?= number_format($todayKg, 1) ?>/30 Kg)
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Metode Pembayaran (COD Saja) -->
                <div class="mb-4">
                    <label class="form-label">METODE PEMBAYARAN</label>
                    <div class="cod-badge">
                        <span style="font-size: 2rem;">💵</span>
                        <div>
                            <div class="fw-bold text-dark">Cash on Delivery (COD)</div>
                            <div class="text-muted small">Bayar langsung secara tunai saat kurir mengantar.</div>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="payment_method_cod" value="cod">
                </div>
                
                <!-- SUMMARY -->
                <div class="summary-box">
                    <h4 class="mb-4">Ringkasan Pesanan</h4>
                    <div class="summary-row">
                        <span class="opacity-75">Subtotal Barang</span>
                        <span id="checkoutSubtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="opacity-75">Ongkos Kirim</span>
                        <span class="text-success fw-bold">Gratis</span>
                    </div>
                    <div class="summary-row total align-items-center">
                        <span class="fs-5 fw-normal">Total Pembayaran</span>
                        <span id="checkoutTotal">Rp 0</span>
                    </div>
                    
                    <button class="btn-place-order mt-4" onclick="placeOrder()" id="placeOrderBtn">
                        <i class="fa-solid fa-lock me-2"></i> Konfirmasi & Proses Pesanan
                    </button>
                    <div class="text-center mt-4 small opacity-50 fw-bold">
                        <i class="fa-solid fa-shield-halved me-1"></i> TRANSAKSI COD AMAN & TERPERCAYA
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function renderCheckout() {
    const container = document.getElementById('checkoutItems');
    if (!container) return;
    const cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
    
    if (cart.length === 0) {
        container.innerHTML = `<div class="text-center text-muted py-5"><i class="fa-solid fa-cart-shopping fa-3x mb-3 opacity-25"></i><p>Keranjang kosong. <a href="<?= base_url('catalog') ?>">Tambah Produk</a></p></div>`;
        return;
    }

    let total = 0;
    let html = '';
    cart.forEach((item, idx) => {
        const sub = item.price * item.qty;
        total += sub;
        html += `
        <div class="d-flex align-items-start mb-4 pb-4 border-bottom">
            <img src="${item.img}" style="width:100px; height:100px; border-radius:12px; object-fit:cover; margin-right:20px;">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between mb-1">
                    <h5 class="fw-bold m-0">${item.name}</h5>
                    <h5 class="fw-bold m-0 text-success">Rp ${sub.toLocaleString('id-ID')}</h5>
                </div>
                <p class="text-muted mb-3 small">Qty: ${item.qty} × Rp ${item.price.toLocaleString('id-ID')}</p>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light border px-2" onclick="chgCheckout(${idx},-1)">−</button>
                    <span class="fw-bold">${item.qty}</span>
                    <button class="btn btn-sm btn-light border px-2" onclick="chgCheckout(${idx},1)">+</button>
                    <button class="btn btn-sm text-danger ms-3" onclick="rmCheckout(${idx})"><i class="fa-regular fa-trash-can me-1"></i>Hapus</button>
                </div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
    document.getElementById('checkoutSubtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('checkoutTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function chgCheckout(idx, delta) {
    let cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
    const item = cart[idx];
    if (delta > 0 && item.qty + delta > (item.maxStock || 999)) {
        Swal.fire({toast:true, position:'top-end', icon:'warning', title: 'Batas stok tercapai', showConfirmButton:false, timer:2000});
        return;
    }
    item.qty += delta;
    if (item.qty <= 0) cart.splice(idx, 1);
    localStorage.setItem('gh_cart', JSON.stringify(cart));
    renderCheckout();
    if (typeof updateCartUI === 'function') updateCartUI();
}

function rmCheckout(idx) {
    let cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
    cart.splice(idx, 1);
    localStorage.setItem('gh_cart', JSON.stringify(cart));
    renderCheckout();
    if (typeof updateCartUI === 'function') updateCartUI();
}

function placeOrder() {
    const cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
    if (cart.length === 0) {
        Swal.fire('Keranjang Kosong', 'Silakan pilih produk terlebih dahulu.', 'warning');
        return;
    }

    <?php if(!session()->get('isLoggedIn')): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Login Diperlukan',
            text: 'Silakan login terlebih dahulu untuk membuat pesanan.',
            confirmButtonColor: '#0b2e21',
            confirmButtonText: 'Login Sekarang'
        }).then(() => { window.location.href = '<?= base_url("auth") ?>'; });
        return;
    <?php endif; ?>

    const address = document.getElementById('deliveryAddress').value;
    if (!address.trim()) {
        Swal.fire('Alamat Kosong', 'Silakan isi alamat pengiriman.', 'warning');
        return;
    }

    const paymentMethod = 'cod';

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Memproses...';

    const formData = new FormData();
    formData.append('items', JSON.stringify(cart));
    formData.append('address', address);
    formData.append('payment_method', paymentMethod);

    fetch('<?= base_url("checkout/process") ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            localStorage.removeItem('gh_cart');
            if (typeof updateCartUI === 'function') updateCartUI();
            Swal.fire({
                icon: 'success',
                title: 'Pesanan Berhasil! 🎉',
                html: `No Order: <strong>${data.order_number}</strong><br><br>${data.message}`,
                confirmButtonColor: '#0b2e21',
                confirmButtonText: 'Lihat Status Pesanan'
            }).then(() => { window.location.href = '<?= base_url("status") ?>'; });
        } else {
            Swal.fire('Gagal', data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i> Konfirmasi & Proses Pesanan';
        }
    })
    .catch(() => {
        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i> Konfirmasi & Proses Pesanan';
    });
}

document.addEventListener('DOMContentLoaded', renderCheckout);
document.addEventListener('turbo:load', renderCheckout);

document.addEventListener('DOMContentLoaded', function() {
    const reorderItems = localStorage.getItem('cartItems');
    if (reorderItems) {
        try {
            const items = JSON.parse(reorderItems);
            if (items.length > 0) {
                let cart = JSON.parse(localStorage.getItem('gh_cart') || '[]');
                items.forEach(item => {
                    const existing = cart.findIndex(c => c.name === item.name);
                    if (existing >= 0) {
                        cart[existing].qty = item.qty;
                    } else {
                        cart.push(item);
                    }
                });
                localStorage.setItem('gh_cart', JSON.stringify(cart));
                localStorage.removeItem('cartItems');
                renderCheckout();
            }
        } catch(e) {}
    }
});
</script>
<?= $this->endSection() ?>
