<?= $this->extend('layout/dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<a href="<?= base_url('owner/reports') ?>" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Laba/Rugi</a>
<a href="<?= base_url('owner/users') ?>" class="nav-item"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
<a href="<?= base_url('owner/monitoring') ?>" class="nav-item active"><i class="fa-solid fa-magnifying-glass-chart"></i> Monitoring</a>
<a href="<?= base_url('owner/settings') ?>" class="nav-item"><i class="fa-solid fa-sliders"></i> Pengaturan & Backup</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div style="font-size:0.75rem; font-weight:700; letter-spacing:2px; color:#112a1f; margin-bottom:8px;">LOGISTIK & FULFILLMENT (READ-ONLY)</div>
    <h2 class="fw-bold mb-2" style="color: #112a1f; font-family:'Playfair Display', serif;">Monitoring Sistem Terpusat</h2>
    <p class="text-muted fs-6 mb-0">Pengawasan jalur pasok (stok operasional) dan status pengiriman (pesanan keluar) tanpa modifikasi/akses ubah.</p>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-4" id="monitorTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active fw-bold px-4 rounded-pill" id="stok-tab" data-bs-toggle="pill" data-bs-target="#stok" type="button" role="tab" style="color:#112a1f;"><i class="fa-solid fa-layer-group me-2"></i> Ketersediaan Stok</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link fw-bold px-4 rounded-pill" id="pesanan-tab" data-bs-toggle="pill" data-bs-target="#pesanan" type="button" role="tab" style="color:#112a1f;"><i class="fa-solid fa-clipboard-check me-2"></i> Jalur Pesanan</button>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="monitorTabsContent">
  
  <!-- TAB STOK -->
  <div class="tab-pane fade show active" id="stok" role="tabpanel">
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                 <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px; width:40%;">KOMODITAS</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">KATEGORI</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">TOTAL PANEN (KG)</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">LOKASI GREENHOUSE</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">UPDATE TERAKHIR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($stocks)): ?>
                        <?php foreach($stocks as $stock): ?>
                            <tr>
                                <td class="ps-3 pt-3 pb-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size:1.05rem;"><?= esc($stock['name']) ?></div>
                                    <div style="font-size:0.8rem; color:#888;"><i class="fa-solid fa-barcode opcity-50 me-1"></i> SKU: <?= esc($stock['sku'] ?? $stock['product_id']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border px-3 py-1 rounded-pill">Grade <?= esc($stock['grade'] ?? 'Standard') ?></span></td>
                                <td>
                                    <div class="fw-bold" style="font-size:1.2rem; color:#112a1f;"><?= number_format(round($stock['total_weight_kg'] ?? 0), 0, ',', '.') ?> <span style="font-size:0.85rem; color:#888;">KG</span></div>
                                </td>
                                <td class="text-muted"><i class="fa-solid fa-location-dot me-1 text-success"></i> Gudang Utama</td>
                                <td class="text-muted font-monospace" style="font-size:0.85rem;">
                                    <?= date('d M Y, H:i', strtotime($stock['last_updated'] ?? date('Y-m-d H:i'))) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada inventaris yang direkam oleh Manager.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>

  <!-- TAB PESANAN -->
  <div class="tab-pane fade" id="pesanan" role="tabpanel">
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                 <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px; width:20%;">NO PENGIRIMAN (AWB)</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">IDENTITAS PELANGGAN</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">NILAI TRANSAKSI</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">KENDALI STATUS</th>
                        <th class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">WAKTU SUBMIT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($orders)): ?>
                        <?php foreach($orders as $row): ?>
                            <tr>
                                <td class="ps-3 pt-3 pb-3">
                                    <div class="fw-bold text-dark mb-1 font-monospace" style="font-size:1.05rem;"><?= esc($row['order_number']) ?></div>
                                    <?php if($row['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success-subtle text-success" style="font-size:0.65rem; letter-spacing:1px;"><i class="fa-solid fa-check me-1"></i> PAID</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger" style="font-size:0.65rem; letter-spacing:1px;"><i class="fa-solid fa-xmark me-1"></i> UNPAID</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size:0.95rem;"><?= esc($row['customer_name']) ?></div>
                                    <div style="font-size:0.8rem; color:#888;">Order via Outlet/Web</div>
                                </td>
                                <td><div class="fw-bold text-dark" style="font-size:1.1rem;">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></div></td>
                                <td>
                                    <?php
                                        $ownerStatusMap = [
                                            'Menunggu Konfirmasi' => ['bg' => 'bg-warning text-dark', 'icon' => 'fa-clock', 'label' => 'Menunggu Konfirmasi'],
                                            'Dikonfirmasi'        => ['bg' => 'bg-info text-dark', 'icon' => 'fa-check', 'label' => 'Dikonfirmasi'],
                                            'Sedang Dipanen'      => ['bg' => 'bg-success-subtle text-success', 'icon' => 'fa-seedling', 'label' => 'Sedang Dipanen'],
                                            'Dalam Pengiriman'    => ['bg' => 'bg-primary', 'icon' => 'fa-truck-fast', 'label' => 'Dalam Pengiriman'],
                                            'Selesai'             => ['bg' => 'bg-success', 'icon' => 'fa-clipboard-check', 'label' => 'Selesai'],
                                            'Dibatalkan'          => ['bg' => 'bg-secondary', 'icon' => 'fa-ban', 'label' => 'Dibatalkan'],
                                        ];
                                        $oSt = $ownerStatusMap[$row['logistic_status']] ?? ['bg' => 'bg-secondary', 'icon' => 'fa-question', 'label' => $row['logistic_status']];
                                    ?>
                                    <span class="badge <?= $oSt['bg'] ?> px-3 py-2 rounded-pill"><i class="fa-solid <?= $oSt['icon'] ?> opacity-50 me-1"></i> <?= $oSt['label'] ?></span>
                                </td>
                                <td class="text-muted" style="font-size:0.9rem;"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pesanan aktif.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>

</div>
<style>
    .nav-pills .nav-link.active { background-color: #eef2f0; border-bottom: 3px solid #112a1f; border-bottom-left-radius: 0; border-bottom-right-radius: 0; }
</style>
<?= $this->endSection() ?>
