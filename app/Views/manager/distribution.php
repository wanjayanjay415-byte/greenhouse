<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('manager') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('manager/stock_report') ?>" class="nav-item"><i class="fa-solid fa-seedling"></i> Stok Sayuran</a>
<a href="<?= base_url('manager/distribution') ?>" class="nav-item active"><i class="fa-solid fa-clipboard-list"></i> Manajemen Pesanan</a>
<a href="<?= base_url('manager/couriers') ?>" class="nav-item"><i class="fa-solid fa-truck-fast"></i> Kelola Kurir</a>
<a href="<?= base_url('manager/users') ?>" class="nav-item"><i class="fa-solid fa-users"></i> Kelola Users</a>
<a href="<?= base_url('manager/reports') ?>" class="nav-item"><i class="fa-solid fa-file-lines"></i> Report Laporan</a>
<a href="<?= base_url('manager/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .log-hub { font-size: 0.75rem; font-weight: 700; letter-spacing: 2px; color: #112a1f; margin-bottom: 12px; display:flex; align-items:center; }
    .log-hub::before { content:''; display:inline-block; width:40px; height:2px; background:#112a1f; margin-right:12px; }
    
    .status-toggle-bar { background: #f0f2f5; border-radius: 12px; padding: 6px; display: inline-flex; flex-wrap: wrap; gap: 4px; }
    .status-toggle-btn { padding: 12px 20px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; color: #555; cursor: pointer; transition: all 0.25s ease; display:inline-flex; align-items:center; gap:8px; border:none; background:transparent; user-select:none;}
    .status-toggle-btn.active { background: #fff; color: #112a1f; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transform: scale(1.02); }
    .status-toggle-btn:hover:not(.active) { color: #112a1f; background: rgba(255,255,255,0.5); }
    .filter-count { background: #0b2e21; color: #fff; font-size: 0.65rem; padding: 2px 8px; border-radius: 20px; font-weight: 800; transition: all 0.25s ease; }
    .status-toggle-btn.active .filter-count { background: #bcf0da; color: #0b2e21; }
    
    .tracker-card { border-radius: 24px; height: 100%; border: none; overflow:hidden; position:relative; transition: transform 0.3s ease; }
    .tracker-card:hover { transform: translateY(-2px); }
    .tracker-light { background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.02); display:flex; }
    .t-left { padding: 40px; z-index:2; position:relative;}
    .t-right { flex-grow:1; background: url('https://images.unsplash.com/photo-1586528116311-ad8ed7c1590a?auto=format&fit=crop&q=80') center/cover; opacity: 0.2; pointer-events:none;}
    .t-gradient { position:absolute; top:0; bottom:0; left:0; width:100%; background: linear-gradient(90deg, #fff 50%, transparent 100%); z-index:1; }
    .tracker-dark { background: #0b2e21; color: #fff; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    
    .table-container { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .table-dist th { font-size: 0.75rem; color: #555; font-weight: 700; letter-spacing: 2px; padding-bottom: 24px; border-bottom: 1px solid #f0f0f0; text-transform: uppercase;}
    .table-dist td { padding: 24px 0; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    
    .ava-circle { width:40px; height:40px; background:#e8ece2; color:#112a1f; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:800; font-size:0.9rem;}
    
    .st-masuk { background: #bcf0da; color: #0b2e21; }
    .st-sortir { background: #eee8db; color: #5c5545; }
    .st-kirim { background: #3b3127; color: #fff; }
    .st-terima { background: #dcdcdc; color: #555; }
    .st-badge { padding: 8px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display:inline-block; letter-spacing:0.5px;}
    
    .btn-update { border: 1px solid #dcdcdc; background: #fff; color: #112a1f; padding: 10px 16px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; letter-spacing:0.5px; cursor:pointer;}
    .btn-update:hover { background: #f0f0f0; }
    .status-select { border: 1px solid #dcdcdc; border-radius: 8px; padding: 8px 12px; font-size: 0.8rem; font-weight: 600; background: #f9fafb; color: #112a1f; cursor:pointer; }
    .status-select:focus { border-color: #0b2e21; outline: none; box-shadow: 0 0 0 3px rgba(11,46,33,0.1); }

    .btn-detail { border: 1px solid #0b2e21; background: #fff; color: #0b2e21; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 0.7rem; letter-spacing:0.5px; cursor:pointer; transition: all 0.2s; }
    .btn-detail:hover { background: #0b2e21; color: #fff; }

    /* Modal Detail Pesanan */
    .od-item-row { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #f0f0f0; }
    .od-item-row:last-child { border-bottom:none; }
    .od-label { font-size:0.7rem; font-weight:700; letter-spacing:1px; color:#888; text-transform:uppercase; margin-bottom:4px; }

    /* Animasi baris tabel */
    .order-row { transition: opacity 0.2s ease, transform 0.2s ease; }
    .order-row.hiding { opacity: 0; transform: translateX(-10px); }
    .order-row.showing { opacity: 1; transform: translateX(0); }
    .empty-state { transition: opacity 0.3s ease; }
</style>

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

<div class="row align-items-center mb-5">
    <div class="col-lg-5">
        <div class="log-hub">LOGISTICS HUB</div>
        <h1 class="fw-bold mb-3" style="color: #112a1f; font-family:'Playfair Display', serif; font-size:3rem; line-height:1.1; letter-spacing:-1px;">Monitoring Pesanan</h1>
        <p class="text-muted fs-6 mb-0" style="max-width:550px;">Real-time surveillance of vegetable circulation from harvest sorting to final consumer delivery.</p>
    </div>
    <div class="col-lg-7 text-end mt-4 mt-lg-0">
        <div class="status-toggle-bar shadow-sm">
            <button class="status-toggle-btn active" onclick="filterOrders('Semua', this)">
                Semua <span class="filter-count" id="count-semua"><?= $totalOrders ?></span>
            </button>
            <button class="status-toggle-btn" onclick="filterOrders('Menunggu Konfirmasi', this)">
                Menunggu <span class="filter-count"><?= $countPending ?></span>
            </button>
            <button class="status-toggle-btn" onclick="filterOrders('Dikonfirmasi', this)">
                Dikonfirmasi <span class="filter-count"><?= $countConfirmed ?></span>
            </button>
            <button class="status-toggle-btn" onclick="filterOrders('Sedang Dipanen', this)">
                Dipanen <span class="filter-count"><?= $countHarvesting ?></span>
            </button>
            <button class="status-toggle-btn" onclick="filterOrders('Dalam Pengiriman', this)">
                Pengiriman <span class="filter-count"><?= $countDelivering ?></span>
            </button>
            <button class="status-toggle-btn" onclick="filterOrders('Selesai', this)">
                Selesai <span class="filter-count"><?= $countDone ?></span>
            </button>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-4">
        <div class="tracker-card tracker-light">
            <div class="t-gradient"></div>
            <div class="t-left">
                <div style="font-size:0.75rem; font-weight:700; letter-spacing:1px; color:#555; margin-bottom:8px;">TOTAL PESANAN</div>
                <div class="d-flex align-items-baseline">
                    <span style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; color:#112a1f;"><?= $totalOrders ?></span>
                    <span class="ms-3 text-muted" style="font-size:0.9rem;">transaksi terdaftar</span>
                </div>
            </div>
            <div class="t-right"></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="tracker-card" style="background:#e8f9ef; padding:40px;">
            <div style="font-size:0.75rem; font-weight:700; letter-spacing:1px; color:#555; margin-bottom:8px;">SEDANG DIKIRIM</div>
            <div class="d-flex align-items-baseline mb-2">
                <span style="font-size:3.5rem; font-weight:800; line-height:1; letter-spacing:-2px; color:#112a1f;"><?= $countDelivering ?></span>
                <span class="ms-3 text-muted" style="font-size:0.9rem;">unit dalam perjalanan</span>
            </div>
            <div class="fw-bold" style="font-size:0.85rem; color:#0b2e21;">
                <i class="fa-solid fa-truck-fast me-2"></i> Active deliveries
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="tracker-card tracker-dark">
            <div style="font-size:0.75rem; font-weight:700; letter-spacing:1px; opacity:0.8; margin-bottom:8px;">SELESAI</div>
            <div class="d-flex align-items-baseline mb-3">
                <span class="display-4 fw-bold m-0 lh-1"><?= $countDone ?></span>
            </div>
            <div style="font-size:0.85rem; font-weight:700;">
                <i class="fa-solid fa-circle-check me-2 text-success"></i> Completed orders
            </div>
        </div>
    </div>
</div>

<!-- Kapasitas Harian Kurir (AKS 2.0) -->
<div class="table-container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold m-0" style="color:#112a1f;"><i class="fa-solid fa-weight-hanging me-2"></i>Kapasitas Muatan Hari Ini</h5>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Batas muatan kurir: <?= $maxDailyKg ?> Kg per hari</p>
        </div>
        <div class="text-end">
            <span class="fw-bold" style="font-size:2rem; color:<?= $todayKg >= 30 ? '#dc2626' : ($todayKg >= 20 ? '#d97706' : '#0b2e21') ?>;"><?= number_format($todayKg, 1) ?></span>
            <span class="text-muted" style="font-size:0.85rem;"> / <?= $maxDailyKg ?> Kg</span>
        </div>
    </div>
    <div class="progress" style="height:14px; border-radius:10px;">
        <?php $pct = min(100, ($todayKg / $maxDailyKg) * 100); ?>
        <div class="progress-bar" role="progressbar" style="width:<?= $pct ?>%; background:<?= $todayKg >= 30 ? '#dc2626' : ($todayKg >= 20 ? '#d97706' : '#16a34a') ?>;" aria-valuenow="<?= $todayKg ?>" aria-valuemin="0" aria-valuemax="<?= $maxDailyKg ?>">
            <?= number_format($pct, 0) ?>%
        </div>
    </div>
    <?php if ($todayKg >= 30): ?>
        <div class="alert alert-danger mt-3 mb-0 fw-bold" style="border-radius:10px; font-size:0.85rem;">
            <i class="fa-solid fa-ban me-2"></i>Kuota pengiriman hari ini sudah PENUH. Pesanan baru akan dijadwalkan ke hari berikutnya.
        </div>
    <?php elseif ($todayKg >= 20): ?>
        <div class="alert alert-warning mt-3 mb-0 fw-bold" style="border-radius:10px; font-size:0.85rem;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>Peringatan: Muatan sudah mencapai <?= number_format($todayKg, 1) ?> Kg. Sisa kuota: <?= number_format(30 - $todayKg, 1) ?> Kg.
        </div>
    <?php endif; ?>
</div>

<div class="table-container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0" style="color:#112a1f;" id="tableTitle">Semua Pesanan</h4>
        <span class="text-muted fw-bold" style="font-size:0.8rem;" id="tableCount"><?= count($orders) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless table-dist align-middle mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th width="12%">ORDER ID</th>
                    <th width="25%">PENERIMA</th>
                    <th width="18%">TOTAL & TANGGAL</th>
                    <th width="15%">STATUS</th>
                    <th width="30%" class="text-end">UBAH STATUS</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                <?php if (!empty($orders)): ?>
                    <?php foreach($orders as $row): ?>
                    <?php
                        // Data pesanan untuk modal detail (di-encode aman ke atribut)
                        $orderJson = htmlspecialchars(json_encode([
                            'order_number' => $row['order_number'],
                            'full_name'    => $row['full_name'],
                            'phone'        => $row['phone'],
                            'email'        => $row['email'],
                            'address'      => $row['delivery_address'] ?? '',
                            'total'        => $row['total_amount'],
                            'created_at'   => $row['created_at'],
                            'status'       => $row['logistic_status'],
                            'payment'      => $row['payment_method'] ?? '',
                            'courier'      => $row['courier_name'] ?? '',
                            'courier_region' => $row['courier_region'] ?? '',
                            'tracking'     => $row['tracking_number'] ?? '',
                            'items'        => $row['items'] ?? [],
                        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr class="order-row showing" data-status="<?= esc($row['logistic_status']) ?>">
                        <td class="fw-bold fs-6 text-dark">
                            #<?= esc($row['order_number']) ?>
                            <button type="button" class="btn-detail mt-2 d-inline-block" data-order='<?= $orderJson ?>' onclick="showOrderDetail(this)">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </button>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="ava-circle"><?= strtoupper(substr($row['full_name'], 0, 2)) ?></div>
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1"><?= esc($row['full_name']) ?></div>
                                    <div class="text-muted" style="font-size:0.7rem; line-height:1.2;"><?= esc($row['phone']) ?><br><?= esc($row['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></div>
                            <div class="text-muted" style="font-size:0.75rem;"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></div>
                        </td>
                        <td>
                            <?php
                                $statusMap = [
                                    'Menunggu Konfirmasi' => ['class' => 'st-masuk', 'icon' => '⏳', 'label' => 'Menunggu Konfirmasi'],
                                    'Dikonfirmasi'        => ['class' => 'st-sortir', 'icon' => '✅', 'label' => 'Dikonfirmasi'],
                                    'Sedang Dipanen'      => ['class' => '', 'style' => 'background:#fef9c3;color:#854d0e;', 'icon' => '🌿', 'label' => 'Sedang Dipanen'],
                                    'Dalam Pengiriman'    => ['class' => 'st-kirim', 'icon' => '🚚', 'label' => 'Dalam Pengiriman'],
                                    'Selesai'             => ['class' => 'st-terima', 'icon' => '✅', 'label' => 'Selesai'],
                                    'Dibatalkan'          => ['class' => '', 'style' => 'background:#fce8e8;color:#dc2626;', 'icon' => '❌', 'label' => 'Dibatalkan'],
                                ];
                                $st = $statusMap[$row['logistic_status']] ?? ['class' => '', 'icon' => '', 'label' => $row['logistic_status']];
                            ?>
                            <span class="st-badge <?= $st['class'] ?>" <?= isset($st['style']) ? 'style="'.$st['style'].'"' : '' ?>>
                                <?= $st['icon'] ?> <?= $st['label'] ?>
                            </span>
                            <div class="mt-1" style="font-size:0.65rem; font-weight:700; color:#888;">
                                💵 COD
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <!-- Order Status Form (AKS 2.0: 5 tahap) -->
                                <form action="<?= base_url('manager/update_order_status') ?>" method="POST" class="d-inline-flex align-items-center gap-2" data-turbo="false">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <select name="new_status" class="status-select" onchange="this.form.submit()">
                                        <option value="Menunggu Konfirmasi" <?= ($row['logistic_status'] == 'Menunggu Konfirmasi') ? 'selected' : '' ?>>⏳ Menunggu Konfirmasi</option>
                                        <option value="Dikonfirmasi" <?= ($row['logistic_status'] == 'Dikonfirmasi') ? 'selected' : '' ?>>✅ Dikonfirmasi</option>
                                        <option value="Sedang Dipanen" <?= ($row['logistic_status'] == 'Sedang Dipanen') ? 'selected' : '' ?>>🌿 Sedang Dipanen</option>
                                        <option value="Dalam Pengiriman" <?= ($row['logistic_status'] == 'Dalam Pengiriman') ? 'selected' : '' ?>>🚚 Dalam Pengiriman</option>
                                        <option value="Selesai" <?= ($row['logistic_status'] == 'Selesai') ? 'selected' : '' ?>>✅ Selesai</option>
                                        <option value="Dibatalkan" <?= ($row['logistic_status'] == 'Dibatalkan') ? 'selected' : '' ?>>❌ Dibatalkan</option>
                                    </select>
                                    <button type="submit" class="btn btn-update d-none">LOGISTIK</button>
                                </form>

                                <!-- Payment Proof & Verification (hanya untuk Transfer) -->
                                <?php if (!empty($row['payment_proof']) && ($row['payment_method'] ?? '') !== 'cod'): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= base_url('uploads/payments/' . $row['payment_proof']) ?>" target="_blank" class="btn btn-sm btn-outline-success fw-bold" style="font-size:0.7rem;">
                                            <i class="fa-solid fa-image"></i> PROOF
                                        </a>
                                        <?php if ($row['payment_status'] == 'pending' && $row['logistic_status'] != 'Dibatalkan'): ?>
                                            <form action="<?= base_url('manager/verify_payment') ?>" method="POST" data-turbo="false">
                                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success fw-bold" style="font-size:0.7rem;">VERIFY PAY</button>
                                            </form>
                                            <form action="<?= base_url('manager/reject_payment') ?>" method="POST" data-turbo="false" onsubmit="return confirm('Tolak pembayaran dan batalkan pesanan? Stok akan dikembalikan.');">
                                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold" style="font-size:0.7rem;">REJECT</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Penugasan Kurir & Resi (Revisi 4.0) -->
                                <?php if ($row['logistic_status'] !== 'Dibatalkan'): ?>
                                    <?php $suggestedIds = $row['suggested_courier_ids'] ?? []; ?>
                                    <form action="<?= base_url('manager/assign_courier') ?>" method="POST" class="d-inline-flex align-items-center gap-2 mt-1" data-turbo="false">
                                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                        <i class="fa-solid fa-truck-fast text-muted" style="font-size:0.8rem;" title="Tugaskan kurir"></i>
                                        <select name="courier_id" class="status-select" style="max-width:190px;" onchange="this.form.submit()">
                                            <option value="">— Pilih Kurir —</option>
                                            <?php foreach ($activeCouriers as $c): ?>
                                                <?php $isSuggested = in_array($c['id'], $suggestedIds); ?>
                                                <option value="<?= $c['id'] ?>" <?= ($row['courier_id'] == $c['id']) ? 'selected' : '' ?>>
                                                    <?= $isSuggested ? '⭐ ' : '' ?><?= esc($c['name']) ?> (<?= esc($c['region']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                    <?php if (!empty($suggestedIds) && empty($row['courier_id'])): ?>
                                        <div style="font-size:0.62rem; font-weight:700; color:#1e40af;">⭐ = wilayah cocok dengan alamat</div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['tracking_number'])): ?>
                                        <div class="mt-1" style="font-size:0.68rem; font-weight:800; color:#0b2e21; letter-spacing:0.5px;">
                                            <i class="fa-solid fa-barcode me-1"></i> RESI: <?= esc($row['tracking_number']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Empty state (hidden by default) -->
        <div class="text-center py-5 empty-state" id="emptyState" style="display:none;">
            <i class="fa-solid fa-inbox fa-3x mb-3 d-block text-muted" style="opacity:0.3;"></i>
            <span class="text-muted" id="emptyMsg">Belum ada data pesanan.</span>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
        <div class="text-muted" style="font-size:0.75rem; font-weight:800; letter-spacing:1.5px;" id="footerInfo">MENAMPILKAN <?= count($orders) ?> DARI <?= $totalOrders ?> PESANAN</div>
    </div>
</div>

<!-- ===== Modal Detail Pesanan ===== -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header text-white" style="background:#0b2e21; border:none;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-receipt me-2"></i> Detail Pesanan <span id="odOrderNumber"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="od-label">Penerima</div>
                        <div class="fw-bold text-dark" id="odCustomer"></div>
                        <div class="text-muted small" id="odContact"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="od-label">Alamat Pengiriman</div>
                        <div class="text-dark" id="odAddress"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="od-label">Tanggal Pesan</div>
                        <div class="text-dark" id="odDate"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="od-label">Status / Pembayaran</div>
                        <div class="text-dark" id="odStatus"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="od-label">Kurir Pengantar</div>
                        <div class="text-dark" id="odCourier"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="od-label">Nomor Resi</div>
                        <div class="text-dark fw-bold" id="odTracking"></div>
                    </div>
                </div>

                <div class="od-label mb-2">Rincian Item</div>
                <div id="odItems"></div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="fw-bold text-muted" style="letter-spacing:1px;">TOTAL</span>
                    <span class="fw-bold fs-4" style="color:#0b2e21;" id="odTotal"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Initialize DataTables (AKS Revisi 2.0)
let dtTable;
$(document).ready(function() {
    dtTable = $('#ordersTable').DataTable({
        language: {
            search: 'Cari pesanan:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ pesanan',
            infoEmpty: 'Tidak ada data pesanan',
            infoFiltered: '(difilter dari _MAX_ total pesanan)',
            zeroRecords: 'Tidak ada pesanan yang cocok',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' }
        },
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50],
        order: [],
        columnDefs: [
            { orderable: false, targets: [4] }
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });
});

// ====== Modal Detail Pesanan ======
function showOrderDetail(btn) {
    const d = JSON.parse(btn.getAttribute('data-order'));
    const rp = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

    document.getElementById('odOrderNumber').textContent = '#' + (d.order_number || '');
    document.getElementById('odCustomer').textContent = d.full_name || '-';
    document.getElementById('odContact').innerHTML = (d.phone || '') + '<br>' + (d.email || '');
    document.getElementById('odAddress').textContent = d.address ? d.address : 'Tidak ada alamat';
    document.getElementById('odTotal').textContent = rp(d.total);

    const dt = d.created_at ? new Date(d.created_at.replace(' ', 'T')) : null;
    document.getElementById('odDate').textContent = dt && !isNaN(dt)
        ? dt.toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })
        : (d.created_at || '-');

    const pay = d.payment === 'cod' ? '💵 COD' : (d.payment ? '🏦 Transfer' : '-');
    document.getElementById('odStatus').innerHTML =
        '<span class="fw-bold">' + (d.status || '-') + '</span> &middot; ' + pay;

    document.getElementById('odCourier').innerHTML = d.courier
        ? '<i class="fa-solid fa-motorcycle me-1 text-success"></i> ' + d.courier + (d.courier_region ? ' <span class="text-muted small">(' + d.courier_region + ')</span>' : '')
        : '<span class="text-muted">Belum ditugaskan</span>';
    document.getElementById('odTracking').innerHTML = d.tracking
        ? '<i class="fa-solid fa-barcode me-1"></i> ' + d.tracking
        : '<span class="text-muted fw-normal">Belum ada resi</span>';

    const items = Array.isArray(d.items) ? d.items : [];
    const wrap = document.getElementById('odItems');
    if (items.length === 0) {
        wrap.innerHTML = '<div class="text-muted small py-2">Tidak ada rincian item.</div>';
    } else {
        wrap.innerHTML = items.map(it =>
            '<div class="od-item-row">' +
                '<div>' +
                    '<span class="fw-bold text-dark">' + (it.product_name || 'Produk') + '</span>' +
                    '<span class="text-muted ms-2">&times; ' + (it.qty || 0) + ' Kg</span>' +
                '</div>' +
                '<span class="fw-bold text-success">' + rp(it.subtotal) + '</span>' +
            '</div>'
        ).join('');
    }

    new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
}

function filterOrders(status, btn) {
    // 1. Update tombol aktif (instan)
    document.querySelectorAll('.status-toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // 2. Use DataTables search for filtering
    if (status === 'Semua') {
        dtTable.column(3).search('').draw();
    } else {
        dtTable.column(3).search(status).draw();
    }

    // 3. Update judul
    const titleEl = document.getElementById('tableTitle');
    titleEl.textContent = (status === 'Semua') ? 'Semua Pesanan' : 'Pesanan: ' + status;
}

// ====== AUTO-SYNC ADMIN (Polling setiap 10 detik) ======
const adminSyncState = {
    total: <?= $totalOrders ?>,
    pending: <?= $countPending ?>,
    confirmed: <?= $countConfirmed ?>,
    harvesting: <?= $countHarvesting ?>,
    delivering: <?= $countDelivering ?>,
    done: <?= $countDone ?>
};

setInterval(() => {
    fetch('<?= base_url("manager/api/check_order_updates") ?>')
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const changed = (
                res.total_orders !== adminSyncState.total ||
                res.count_pending !== adminSyncState.pending ||
                res.count_confirmed !== adminSyncState.confirmed ||
                res.count_harvesting !== adminSyncState.harvesting ||
                res.count_delivering !== adminSyncState.delivering ||
                res.count_done !== adminSyncState.done
            );

            if (changed) {
                // Tampilkan indikator sync
                const header = document.querySelector('.log-hub');
                if (header) {
                    header.innerHTML = '<i class="fa-solid fa-arrows-rotate fa-spin me-2 text-success"></i> MENYINKRONKAN DATA BARU...';
                }
                setTimeout(() => window.location.reload(), 1500);
            }
        }
    })
    .catch(err => console.log('Admin auto-sync error:', err));
}, 10000);
</script>
<?= $this->endSection() ?>
