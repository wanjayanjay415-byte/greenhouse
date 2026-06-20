<?= $this->extend('layout/customer') ?>
<?= $this->section('content') ?>

<!-- Pastikan data selalu fresh, tidak ambil dari cache Turbo -->
<meta name="turbo-cache-control" content="no-cache">

<style>
    body { background-color: #fafbfc; }
    .status-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 24px; border:1px solid #f0f0f0; }
    
    .stepper { display: flex; justify-content: space-between; align-items: center; position: relative; padding: 0 10px; margin: 30px 0;}
    .stepper::before { content: ''; position: absolute; top: 18px; left: 40px; right: 40px; height: 3px; background: #eaeaea; z-index: 1;}
    .step-item { position: relative; z-index: 2; text-align: center; width: 100px; display: flex; flex-direction: column; align-items: center;}
    .step-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: bold; background: #fff; border: 3px solid #eaeaea; color: #ccc; margin-bottom: 12px; transition: 0.3s;}
    
    .step-item.done .step-icon { background: #0b2e21; border-color: #0b2e21; color: #fff;}
    .step-item.done .step-title { color: #0b2e21; font-weight: 700; }
    
    .step-item.current .step-icon { background: #f59e0b !important; border-color: #f59e0b !important; color: #fff !important; box-shadow: 0 0 0 4px rgba(245,158,11,0.2); animation: pulse 2s infinite; }
    .step-item.current .step-title { color: #b45309; font-weight: 800; }

    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    
    .step-title { font-size: 0.75rem; color: #999; margin-bottom: 4px; transition: 0.3s;}
    .step-desc { font-size: 0.7rem; color: #888;}
    
    .btn-reorder { background: #0b2e21; color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-cancel { background: #fff; color: #dc2626; border: 1.5px solid #dc2626; border-radius: 8px; padding: 8px 16px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-cancel:hover { background: #dc2626; color: #fff; }
    .btn-reorder:hover { background: #1a4a35; color: #fff; }
    
    .btn-custom { background: #0b2e21; color: #fff; border: none; border-radius: 8px; padding: 10px 16px; font-weight: 700; transition: 0.3s; }
    .btn-custom:hover { background: #1a4a35; color: #fff; }

    .history-timeline { border-left: 3px solid #e5e7eb; padding-left: 16px; margin-left: 8px; }
    .history-item { position: relative; padding-bottom: 12px; }
    .history-item::before { content: ''; position: absolute; left: -22px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #0b2e21; border: 2px solid #fff; }
</style>

<div class="container-fluid px-5 py-4 mb-5">
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <h1 class="display-6 fw-bold" style="font-family:'Playfair Display', serif; color:#112a1f;">Status Pesanan Anda</h1>
            <p class="text-muted fs-5 mb-0">Pantau semua pesanan Anda secara real-time.</p>
        </div>
        <button class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="window.location.reload()"><i class="fa-solid fa-rotate me-1"></i> Perbarui Data</button>
    </div>

    <!-- Filter Form -->
    <div class="status-card mb-4">
        <form action="<?= base_url('status') ?>" method="GET" class="row g-3 align-items-end" data-turbo="false">
            <div class="col-md-4">
                <label class="form-label" style="font-size:0.75rem; font-weight:700; color:#555;">TANGGAL MULAI</label>
                <input type="date" class="form-control" name="start_date" value="<?= esc($start_date) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:0.75rem; font-weight:700; color:#555;">TANGGAL AKHIR</label>
                <input type="date" class="form-control" name="end_date" value="<?= esc($end_date) ?>" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-filter me-2"></i> Filter Pesanan</button>
            </div>
        </form>
    </div>

    <?php if (!session()->get('isLoggedIn')): ?>
        <div class="status-card text-center py-5">
            <i class="fa-solid fa-lock fa-3x mb-3 opacity-25"></i>
            <h5 class="fw-bold text-muted">Silakan login untuk melihat pesanan Anda</h5>
            <a href="<?= base_url('auth') ?>" class="btn btn-custom mt-3 px-4">Masuk Sekarang</a>
        </div>
    <?php elseif (empty($orders)): ?>
        <div class="status-card text-center py-5">
            <i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i>
            <h5 class="fw-bold text-muted">Belum ada pesanan</h5>
            <p class="text-muted">Anda belum membuat pesanan apapun dalam 1 bulan terakhir.</p>
            <a href="<?= base_url('catalog') ?>" class="btn btn-custom mt-2 px-4">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <?php 
        // AKS 2.0: 5 tahap status linier
        $statusSteps = ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Sedang Dipanen', 'Dalam Pengiriman', 'Selesai'];
        $stepIcons = ['⏳', '✅', '🌿', '🚚', '✅'];
        foreach ($orders as $order): 
            $currentIdx = array_search($order['logistic_status'], $statusSteps);
            if ($currentIdx === false) $currentIdx = -1; // Dibatalkan
        ?>
        <div class="status-card" data-order-id="<?= $order['id'] ?>">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color:#112a1f;">
                        <i class="fa-solid fa-box me-2 opacity-50"></i>
                        <?= esc($order['order_number']) ?>
                    </h4>
                    <div class="text-muted" style="font-size:0.9rem;">
                        Dipesan: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                        <?php if (!empty($order['payment_method'])): ?>
                            · <span class="badge bg-light text-dark border"><?= $order['payment_method'] === 'cod' ? '💵 COD' : '🏦 Transfer' ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-5 text-success">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></div>
                    <?php if ($order['logistic_status'] === 'Selesai'): ?>
                        <span class="badge bg-success rounded-pill px-3 py-1">✅ Selesai</span>
                    <?php elseif(($order['payment_method'] ?? '') === 'cod'): ?>
                        <span class="badge bg-success rounded-pill px-3 py-1">💵 COD — Bayar Saat Terima</span>
                    <?php elseif($order['payment_status'] == 'paid'): ?>
                        <span class="badge bg-success rounded-pill px-3 py-1">✅ Lunas</span>
                    <?php elseif($order['payment_status'] == 'failed'): ?>
                        <span class="badge bg-danger rounded-pill px-3 py-1">Gagal</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Menunggu Bayar</span>
                    <?php endif; ?>
                    <div class="text-muted mt-2" style="font-size:0.7rem;">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Update: <?= date('H:i', strtotime($order['updated_at'] ?? $order['created_at'])) ?>
                    </div>
                </div>
            </div>

            <?php if ($order['logistic_status'] == 'Dibatalkan'): ?>
                <div class="alert alert-danger mb-0 py-2 d-inline-block rounded-pill px-4">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Pesanan Dibatalkan
                </div>
            <?php else: ?>
                <!-- Stepper 5 Tahap (AKS 2.0) -->
                <div class="stepper">
                    <?php foreach ($statusSteps as $idx => $step): ?>
                        <?php
                            $class = '';
                            if ($idx < $currentIdx) $class = 'done';
                            elseif ($idx == $currentIdx) $class = 'done current';
                        ?>
                        <div class="step-item <?= $class ?>">
                            <div class="step-icon">
                                <?php if ($idx < $currentIdx): ?>
                                    <i class="fa-solid fa-check"></i>
                                <?php elseif ($idx == $currentIdx): ?>
                                    <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i>
                                <?php else: ?>
                                    <?= $idx + 1 ?>
                                <?php endif; ?>
                            </div>
                            <div class="step-title"><?= $step ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Keterangan Estimasi per Status (AKS) -->
                <?php if ($order['logistic_status'] === 'Sedang Dipanen'): ?>
                    <div class="alert d-flex align-items-center gap-2 mb-0 py-2 px-3 rounded-3" style="background:#fef9c3; color:#854d0e; border:1px solid #fde68a; font-size:0.85rem;">
                        <span style="font-size:1.1rem;">🌿</span>
                        <span><strong>Estimasi panen ±2 hari.</strong> Sayuran Anda sedang dipanen segar dari greenhouse.</span>
                    </div>
                <?php elseif ($order['logistic_status'] === 'Dalam Pengiriman'): ?>
                    <div class="alert d-flex align-items-center gap-2 mb-0 py-2 px-3 rounded-3" style="background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; font-size:0.85rem;">
                        <span style="font-size:1.1rem;">🚚</span>
                        <span><strong>Estimasi pengiriman ±2 jam.</strong> Pesanan Anda sedang dalam perjalanan ke alamat tujuan.</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Order Items -->
            <?php if (!empty($order['items'])): ?>
            <div class="mt-3 p-3 rounded-3" style="background:#f7f9fa;">
                <div class="fw-bold text-muted mb-2" style="font-size:0.75rem; letter-spacing:1px;">ITEM PESANAN:</div>
                <?php foreach ($order['items'] as $item): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:#eaeaea !important;">
                    <div>
                        <span class="fw-bold"><?= esc($item['product_name']) ?></span>
                        <span class="text-muted ms-2">× <?= $item['qty'] ?> Kg</span>
                    </div>
                    <span class="fw-bold text-success">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($order['delivery_address']): ?>
            <div class="mt-3 text-muted" style="font-size:0.85rem;">
                <i class="fa-solid fa-location-dot me-1"></i> <?= esc($order['delivery_address']) ?>
            </div>
            <?php endif; ?>

            <!-- Info Kurir & Resi (Revisi 4.0) -->
            <?php if (!empty($order['tracking_number']) || !empty($order['courier_name'])): ?>
            <div class="mt-3 p-3 rounded-3 d-flex flex-wrap gap-3 align-items-center" style="background:#eef6f1; border:1px solid #d4e9dd;">
                <?php if (!empty($order['courier_name'])): ?>
                <div style="font-size:0.85rem;">
                    <div class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:0.5px;">KURIR PENGANTAR</div>
                    <span class="fw-bold" style="color:#0b2e21;"><i class="fa-solid fa-motorcycle me-1"></i> <?= esc($order['courier_name']) ?></span>
                    <?php if (!empty($order['courier_phone'])): ?>
                        <span class="text-muted ms-1">· <?= esc($order['courier_phone']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($order['tracking_number'])): ?>
                <div style="font-size:0.85rem;">
                    <div class="text-muted" style="font-size:0.7rem; font-weight:700; letter-spacing:0.5px;">NOMOR RESI</div>
                    <span class="fw-bold" style="color:#0b2e21; letter-spacing:0.5px;"><i class="fa-solid fa-barcode me-1"></i> <?= esc($order['tracking_number']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($order['payment_proof'])): ?>
                <div class="mt-3">
                    <a href="<?= base_url('uploads/payments/' . $order['payment_proof']) ?>" target="_blank" class="text-decoration-none text-success fw-bold" style="font-size:0.85rem;">
                        <i class="fa-solid fa-image me-1"></i> Lihat Bukti Transfer
                    </a>
                </div>
            <?php endif; ?>

            <!-- Status History Timeline (AKS 2.0) -->
            <?php if (!empty($order['status_history'])): ?>
            <details class="mt-3">
                <summary class="fw-bold text-muted" style="font-size:0.8rem; cursor:pointer;">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Histori Perubahan Status (<?= count($order['status_history']) ?>)
                </summary>
                <div class="history-timeline mt-3">
                    <?php foreach ($order['status_history'] as $h): ?>
                    <div class="history-item">
                        <div class="fw-bold" style="font-size:0.8rem; color:#112a1f;"><?= esc($h['new_status']) ?></div>
                        <div class="text-muted" style="font-size:0.7rem;">
                            <?= date('d M Y H:i', strtotime($h['changed_at'])) ?>
                            <?php if (!empty($h['changed_by_name'])): ?>
                                · oleh <?= esc($h['changed_by_name']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <!-- Tombol Aksi (AKS 2.0) -->
            <?php if ($order['logistic_status'] === 'Selesai' || $order['logistic_status'] === 'Dibatalkan'): ?>
            <div class="mt-3 pt-3 border-top">
                <button class="btn-reorder" onclick="reorder(<?= $order['id'] ?>)">
                    <i class="fa-solid fa-rotate-left me-1"></i> Pesan Ulang
                </button>
            </div>
            <?php elseif ($order['logistic_status'] === 'Menunggu Konfirmasi'): ?>
            <div class="mt-3 pt-3 border-top">
                <button class="btn-cancel" onclick="cancelOrder(<?= $order['id'] ?>)">
                    <i class="fa-solid fa-xmark me-1"></i> Batalkan Pesanan
                </button>
                <small class="text-muted ms-2">Pesanan masih bisa dibatalkan sebelum dikonfirmasi admin.</small>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (session()->get('isLoggedIn') && !empty($orders)): ?>
<script>
    // Menyimpan state pesanan saat ini
    const currentOrders = <?= json_encode(array_map(function($o) { 
        return [
            'id' => $o['id'], 
            'logistic_status' => $o['logistic_status'], 
            'payment_status' => $o['payment_status']
        ]; 
    }, $orders)) ?>;

    // Polling setiap 5 detik untuk sinkronisasi otomatis
    setInterval(() => {
        fetch('<?= base_url("api/check_order_updates") ?>')
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                let isChanged = false;
                
                if (res.data.length !== currentOrders.length) {
                    isChanged = true;
                } else {
                    for (let i = 0; i < res.data.length; i++) {
                        const latest = res.data[i];
                        const current = currentOrders.find(o => o.id == latest.id);
                        if (!current || 
                            current.logistic_status !== latest.logistic_status || 
                            current.payment_status !== latest.payment_status) {
                            isChanged = true;
                            break;
                        }
                    }
                }

                if (isChanged) {
                    const btn = document.querySelector('button[onclick="window.location.reload()"]');
                    if (btn) {
                        btn.innerHTML = '<i class="fa-solid fa-arrows-rotate fa-spin me-1"></i> Menyinkronkan...';
                        btn.classList.remove('btn-outline-dark');
                        btn.classList.add('btn-success', 'text-white');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                }
            }
        })
        .catch(err => console.log('Auto-sync error:', err));
    }, 5000);

    // AKS 2.0: Batalkan Pesanan (hanya saat Menunggu Konfirmasi)
    function cancelOrder(orderId) {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            text: 'Pesanan akan dibatalkan dan stok dikembalikan. Tindakan ini tidak dapat diurungkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (!result.isConfirmed) return;
            fetch('<?= base_url("order/cancel") ?>/' + orderId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success', title: 'Dibatalkan', text: res.message,
                        timer: 1600, showConfirmButton: false
                    });
                    setTimeout(() => window.location.reload(), 1700);
                } else {
                    Swal.fire('Gagal', res.message || 'Tidak dapat membatalkan pesanan.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            });
        });
    }

    // AKS 2.0: Pesan Ulang
    function reorder(orderId) {
        fetch('<?= base_url("api/reorder") ?>/' + orderId)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.items) {
                // Set cart items di localStorage
                let cart = [];
                res.items.forEach(item => {
                    cart.push({
                        name: item.name,
                        price: item.price,
                        qty: Math.min(item.qty, item.maxStock), // jangan melebihi stok
                        img: '',
                        maxStock: item.maxStock
                    });
                });
                localStorage.setItem('cartItems', JSON.stringify(cart));
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: 'Item disalin ke keranjang!',
                        showConfirmButton: false, timer: 1500
                    });
                }
                setTimeout(() => window.location.href = '<?= base_url("checkout") ?>', 1600);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', res.message || 'Tidak dapat memuat item.', 'error');
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            }
        });
    }
</script>
<?php endif; ?>
<?= $this->endSection() ?>
