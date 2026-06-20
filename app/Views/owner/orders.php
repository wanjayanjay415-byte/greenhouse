<?= $this->extend('layout/manager_dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('owner/stock') ?>" class="nav-item"><i class="fa-solid fa-leaf"></i> Stock Input</a>
<a href="<?= base_url('owner/orders') ?>" class="nav-item active"><i class="fa-solid fa-clipboard-list"></i> Order Management</a>
<a href="#" class="nav-item" onclick="showNotifModal()"><i class="fa-regular fa-bell"></i> Notifications</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .banner-prep { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); display: flex; align-items: stretch; margin-bottom: 30px; position: relative;}
    .prep-left { padding: 40px; flex-grow: 1; position: relative; z-index: 2; }
    .prep-right { background: #0b2e21; color: #fff; padding: 40px; width: 350px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center; position: relative;}
    .prep-right::before { content: ''; position: absolute; left:-20px; top:0; bottom:0; width: 40px; background: #0b2e21; transform: skewX(-10deg); z-index: 1;}
    .prep-right > div { position: relative; z-index: 3; }
    
    .badge-status-top { background: #0b2e21; color: #fff; border-radius: 20px; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display:inline-block; margin-bottom: 20px;}
    .badge-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:#a3cfba; margin-right:6px;}
    
    .avatar-stack { display:flex; align-items:center; }
    .avatar-stack img, .avatar-stack .more { width: 44px; height: 44px; border-radius: 50%; border: 3px solid #fff; margin-left: -12px; object-fit: cover;}
    .avatar-stack img:first-child { margin-left: 0; }
    .avatar-stack .more { background: #a3cfba; color: #0b2e21; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem;}
    
    .table-container { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .init-badge { width: 40px; height: 40px; border-radius: 50%; background: #eef2f0; color: #112a1f; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; margin-right: 16px; }
    
    .b-status { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #eaeaea; text-transform: uppercase; letter-spacing: 0.5px;}
    .b-proses { background: #e8f4ed; color: #2e7d32; border-color: #d1e8d9;}
    .b-antrian { background: #fdf3e7; color: #d97706; border-color: #fce4ce;}
    .b-kirim { background: #fffaf0; color: #b45309; border-color: #ffedd5;}
    .b-selesai { background: #f7f9fa; color: #555; }
    
    .table td { vertical-align: middle; padding: 20px 0; border-bottom: 1px solid #f5f5f5;}
</style>

<!-- Top Alert -->
<div class="alert bg-dark text-white rounded-3 shadow-sm d-flex justify-content-between align-items-center p-3 mb-4" style="background-color: #0b2e21 !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="btn btn-sm btn-light rounded-circle" style="background: rgba(255,255,255,0.1); border:none; color:#a3cfba;"><i class="fa-regular fa-bell"></i></div>
        <div>
            <span style="font-size:0.7rem; font-weight:700; letter-spacing:1px; color:#a3cfba; display:block;">STATUS UPDATE</span>
            <span class="fw-bold fs-6">Order #ARC-9023 marked as <span class="bg-warning text-dark px-2 py-1 rounded ms-1" style="font-size:0.8rem; font-weight:800;">KIRIM</span></span>
        </div>
    </div>
    <button class="btn-close btn-close-white"></button>
</div>

<!-- Banner Preparations -->
<div class="banner-prep">
    <div class="prep-left">
        <div class="d-flex gap-3 mb-4">
            <div class="badge-status-top mb-0" style="background: #112a1f;">LIVE STATS</div>
            <div class="badge-status-top mb-0" style="background:transparent; color:#555;"><span class="badge-dot" style="background:#2ed573;"></span> 2 STATIONS ACTIVE</div>
        </div>
        <h1 class="fw-bold mb-3" style="color:#112a1f; font-size:2.8rem; letter-spacing:-1px;">Active Preparations</h1>
        <p class="text-muted fs-5 mb-5" style="max-width: 500px;">Four orders are currently being curated at the Greenhouse Alpha and Beta stations for immediate shipment.</p>
        
        <div class="d-flex align-items-center gap-5">
            <div class="avatar-stack">
                <img src="https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&q=80&w=100" alt="Plant 1">
                <img src="https://images.unsplash.com/photo-1603507024467-3cce7a05da93?auto=format&fit=crop&q=80&w=100" alt="Plant 2">
                <div class="more">+2</div>
            </div>
            <div class="border-start ps-4">
                <div style="font-size:0.75rem; font-weight:700; color:#888; letter-spacing:1px;">AVG. TIME</div>
                <div class="fw-bold fs-4 text-dark">12m 45s</div>
            </div>
        </div>
    </div>
    <div class="prep-right">
        <div>
            <h3 class="fw-bold mb-1">Ready for Dispatch</h3>
            <div class="d-flex align-items-end justify-content-between mt-3">
                <h1 class="display-3 fw-bold m-0 lh-1">82%</h1>
                <div class="text-end">
                    <i class="fa-solid fa-truck-fast fs-3 mb-2" style="color:#a3cfba;"></i>
                    <div style="font-size:0.7rem; font-weight:700; letter-spacing:1px; color:#a3cfba;">TARGET: 95%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Queue Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Order Queue</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">Monitoring 24 active shipments and pickups</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border fw-semibold rounded-pill px-4"><i class="fa-solid fa-filter me-2 text-muted"></i> FILTER</button>
            <button class="btn btn-light border fw-semibold rounded-pill px-4"><i class="fa-solid fa-download me-2 text-muted"></i> EXPORT CSV</button>
        </div>
    </div>
    
    <div class="table-responsive mt-4">
        <table class="table table-borderless align-middle">
            <thead style="font-size:0.75rem; font-weight:700; color:#888; letter-spacing:1px; border-bottom:1px solid #eaeaea;">
                <tr>
                    <td class="pb-3">ORDER<br>ID</td>
                    <td class="pb-3">CUSTOMER</td>
                    <td class="pb-3">ITEMS</td>
                    <td class="pb-3">SUBTOTAL</td>
                    <td class="pb-3 text-center">STATUS</td>
                    <td class="pb-3">LAST<br>UPDATE</td>
                    <td class="pb-3 text-end">UPDATE<br>STATUS</td>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <tr>
                    <td class="fw-bold text-dark fs-6">#ARC-<br>9021</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="init-badge" style="background:#e8ece2;">EJ</div>
                            <div>
                                <div class="fw-bold text-dark">Eleanor<br>Jacobs</div>
                                <div class="text-muted" style="font-size:0.65rem;">Retail Partner</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark mb-1" style="font-size:0.9rem;">3x<br>Monstera<br>Deliciosa...</div>
                    </td>
                    <td class="fw-bold text-dark fs-5">$452.00</td>
                    <td class="text-center"><span class="b-status b-proses"><span class="text-success me-1">&bull;</span> PROSES</span></td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">5m ago</div>
                        <div class="text-muted" style="font-size:0.75rem;">Processing</div>
                    </td>
                    <td class="text-end">
                        <select class="form-select form-select-sm fw-semibold bg-light border-0 d-inline-block rounded-pill ps-3" style="width:100px; color:#555;" onchange="alert('Status updating...')">
                            <option selected>Proses</option>
                            <option>Antrian</option>
                            <option>Kirim</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Row 2 -->
                <tr>
                    <td class="fw-bold text-dark fs-6">#ARC-<br>9022</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="init-badge" style="background:#0b2e21; color:#fff;">MA</div>
                            <div>
                                <div class="fw-bold text-dark">Marcus<br>Bennett</div>
                                <div class="text-muted" style="font-size:0.65rem;">Collector</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark mb-1" style="font-size:0.9rem;">1x<br>Alocasia<br>Frydek</div>
                    </td>
                    <td class="fw-bold text-dark fs-5">$120.00</td>
                    <td class="text-center"><span class="b-status b-antrian"><i class="fa-regular fa-clock me-1"></i> ANTRIAN</span></td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">15m ago</div>
                        <div class="text-muted" style="font-size:0.75rem;">Queued</div>
                    </td>
                    <td class="text-end">
                        <select class="form-select form-select-sm fw-semibold bg-light border-0 d-inline-block rounded-pill ps-3" style="width:100px; color:#555;" onchange="alert('Status updating...')">
                            <option selected>Proses</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Row 3 -->
                <tr>
                    <td class="fw-bold text-dark fs-6">#ARC-<br>9023</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="init-badge" style="background:#e4e2c7;">SK</div>
                            <div>
                                <div class="fw-bold text-dark">Sarah<br>Kim</div>
                                <div class="text-muted" style="font-size:0.65rem;">Standard<br>Order</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark mb-1" style="font-size:0.9rem;">5x<br>Succulent<br>Box</div>
                    </td>
                    <td class="fw-bold text-dark fs-5">$85.50</td>
                    <td class="text-center"><span class="b-status b-kirim"><i class="fa-solid fa-truck-fast me-1"></i> KIRIM</span></td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">2m ago</div>
                        <div class="text-muted" style="font-size:0.75rem;">Marked<br>Dispatch</div>
                    </td>
                    <td class="text-end">
                        <select class="form-select form-select-sm fw-semibold bg-light border-0 d-inline-block rounded-pill ps-3" style="width:100px; color:#555;" onchange="alert('Status updating...')">
                            <option selected>Kirim</option>
                        </select>
                    </td>
                </tr>
                
                <!-- Row 4 -->
                <tr>
                    <td class="fw-bold text-dark fs-6">#ARC-<br>9024</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="init-badge" style="background:#f0f3f2;">DW</div>
                            <div>
                                <div class="fw-bold text-dark">David<br>Wilson</div>
                                <div class="text-muted" style="font-size:0.65rem;">Local Pickup</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark mb-1" style="font-size:0.9rem;">2x Rubber<br>Tree</div>
                    </td>
                    <td class="fw-bold text-dark fs-5">$190.00</td>
                    <td class="text-center"><span class="b-status b-selesai"><i class="fa-solid fa-check me-1 text-success"></i> SELESAI</span></td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">1h ago</div>
                        <div class="text-muted" style="font-size:0.75rem;">Delivered</div>
                    </td>
                    <td class="text-end">
                        <select class="form-select form-select-sm fw-semibold bg-light border-0 d-inline-block rounded-pill ps-3" style="width:100px; color:#555;" disabled>
                            <option selected>Selesai</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-3 pt-4 border-top">
        <div class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">SHOWING 1 TO 4 OF 24 ENTRIES</div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light border bg-white rounded-circle" style="width:32px; height:32px;"><i class="fa-solid fa-chevron-left text-muted"></i></button>
            <button class="btn btn-sm btn-dark text-white fw-bold rounded-circle" style="width:32px; height:32px; background:#0b2e21;">1</button>
            <button class="btn btn-sm btn-light text-dark fw-bold border-0 bg-transparent" style="width:32px; height:32px;">2</button>
            <button class="btn btn-sm btn-light text-dark fw-bold border-0 bg-transparent" style="width:32px; height:32px;">3</button>
            <button class="btn btn-sm btn-light border bg-white rounded-circle" style="width:32px; height:32px;"><i class="fa-solid fa-chevron-right text-muted"></i></button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
