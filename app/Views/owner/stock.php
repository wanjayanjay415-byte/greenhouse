<?= $this->extend('layout/manager_dashboard') ?>

<?= $this->section('sidebar_menus') ?>
<a href="<?= base_url('owner') ?>" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
<a href="<?= base_url('owner/stock') ?>" class="nav-item active"><i class="fa-solid fa-leaf"></i> Stock Input</a>
<a href="<?= base_url('owner/orders') ?>" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Order Management</a>
<a href="#" class="nav-item" onclick="showNotifModal()"><i class="fa-regular fa-bell"></i> Notifications</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .input-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); height: 100%; border: 1px solid #f0f0f0; }
    .card-title-icon { width: 44px; height: 44px; background: #e8f4ed; border-radius: 50%; color: #0b2e21; display:flex; align-items:center; justify-content:center; margin-bottom: 24px; font-size:1.2rem;}
    .form-label { font-size: 0.75rem; font-weight: 700; color: #777; letter-spacing: 1px; margin-bottom: 8px; text-transform:uppercase;}
    
    .yield-input-group { background: #f7f9fa; border-radius: 8px; display:flex; align-items:center; padding-right: 16px; margin-bottom: 24px; border: 1px solid transparent;}
    .yield-input-group:focus-within { border-color:#0b2e21; }
    .yield-input-group input { background: transparent; border:none; padding: 12px 16px; outline:none; font-size: 1.1rem; width:100%; font-weight:600;}
    .yield-input-group span { font-weight: 700; font-size: 0.8rem; color:#555;}
    
    .grade-badge { padding: 10px 16px; border-radius: 8px; background: #f7f9fa; color: #555; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-align:center; transition:0.2s;}
    .grade-badge:hover { background: #eaeaea; }
    .grade-badge.active { background: #0b2e21; color: #fff; }
    
    .btn-submit { background: #0b2e21; color: #fff; border-radius: 8px; padding: 16px; font-weight: 600; width: 100%; border: none; transition:0.3s; margin-top:24px;}
    .btn-submit:hover { background: #164734; }
    
    .summary-card { background: #0b2e21; color: #fff; border-radius: 20px; padding: 30px; margin-top: 20px; position:relative; overflow:hidden;}
    .summary-card::after { content:''; position:absolute; bottom:-10px; right:10px; width:120px; height:120px; background: url('https://cdn-icons-png.flaticon.com/512/2928/2928883.png') no-repeat center/contain; opacity:0.1; filter:invert(1);}
    
    .table-list img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; margin-right: 16px; }
    .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display:inline-block; }
    .status-ada { background: #e8f4ed; color: #0b2e21; border: 1px solid #d1e8d9; }
    .status-kosong { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .status-critical { background: #dc2626; color: #fff; display:block; margin-top:4px; font-size:0.65rem; text-align:center; padding: 4px; border-radius:12px;}
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h1 class="fw-bold mb-1" style="color:#112a1f;">Harvest Ledger</h1>
        <p class="text-muted" style="font-size:0.95rem;">Precision documentation of greenhouse yield. Maintain high-fidelity<br>records of current stock levels and botanical grades for the central archive.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light border fw-semibold shadow-sm"><i class="fa-solid fa-file-excel me-2"></i> Export Excel</button>
        <button class="btn btn-light border fw-semibold shadow-sm"><i class="fa-solid fa-file-pdf me-2"></i> Export PDF</button>
        <button class="btn btn-dark-green fw-semibold shadow-sm px-4"><i class="fa-solid fa-plus me-2"></i> Manual Update</button>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Left Area: Input Form -->
    <div class="col-lg-4">
        <div class="input-card mb-4 mt-0">
            <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                <div class="card-title-icon m-0 me-3"><i class="fa-solid fa-pen-nib"></i></div>
                <div>
                    <h5 class="fw-bold m-0 text-dark">Input Hasil</h5>
                    <div class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">NEW HARVEST ENTRY</div>
                </div>
            </div>
            
            <form id="stockForm" onsubmit="event.preventDefault(); submitForm();">
                <label class="form-label">VEGETABLE VARIETY</label>
                <select class="form-select mb-4 py-2 bg-light fw-semibold border-0">
                    <option>Bayam Organik</option>
                    <option>Kangkung Hidroponik</option>
                    <option>Selada Keriting</option>
                    <option>Pakcoy Premium</option>
                </select>
                
                <label class="form-label">YIELD WEIGHT</label>
                <div class="yield-input-group">
                    <input type="number" step="0.01" placeholder="0.00" required>
                    <span>KILOGRAMS</span>
                </div>
                
                <label class="form-label">BOTANICAL GRADE</label>
                <div class="row g-2 mb-2">
                    <div class="col-4"><div class="grade-badge active" onclick="activateGrade(this)">Grade A</div></div>
                    <div class="col-4"><div class="grade-badge" onclick="activateGrade(this)">Grade B</div></div>
                    <div class="col-4"><div class="grade-badge" onclick="activateGrade(this)">Grade C</div></div>
                </div>
                
                <button type="submit" class="btn-submit">Simpan Inventaris</button>
            </form>
        </div>
        
        <div class="summary-card shadow-sm">
            <div style="font-size:0.75rem; font-weight:700; letter-spacing:1px; opacity:0.8; margin-bottom:8px;">TOTAL YIELD TODAY</div>
            <div class="d-flex align-items-baseline mb-3">
                <span style="font-size:3rem; font-weight:800; line-height:1;">128.4</span>
                <span class="ms-2 fs-5">kg</span>
            </div>
            <div class="d-inline-flex align-items-center bg-light text-success fw-bold rounded-pill px-2 py-1" style="font-size:0.75rem;">
                <i class="fa-solid fa-arrow-trend-up me-1"></i> +12% vs. 24h previous average
            </div>
        </div>
    </div>
    
    <!-- Right Area: Table List -->
    <div class="col-lg-8">
        <div class="input-card pb-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold m-0 text-dark">Stock Inventory</h5>
                    <div class="text-muted" style="font-size:0.75rem; font-weight:700; letter-spacing:1px;">LIVE ARCHIVE RECORDS</div>
                </div>
                <div>
                    <button class="btn btn-light rounded-circle shadow-sm border"><i class="fa-solid fa-filter"></i></button>
                    <button class="btn btn-light rounded-circle shadow-sm border ms-2"><i class="fa-solid fa-sort"></i></button>
                </div>
            </div>
            
            <div class="table-responsive mt-4">
                <table class="table table-borderless table-list align-middle mb-0">
                    <thead style="font-size:0.75rem; font-weight:700; color:#888; letter-spacing:1px; border-bottom:1px solid #eaeaea;">
                        <tr>
                            <td class="pb-3 px-3">NO</td>
                            <td class="pb-3">BOTANICAL VARIETY</td>
                            <td class="pb-3">QTY<br>(KG)</td>
                            <td class="pb-3">STATUS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom border-light">
                            <td class="py-4 px-3 text-muted fw-bold">01</td>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&q=80&w=100" alt="Bayam">
                                    <div class="fw-bold text-dark">Bayam<br>Organik</div>
                                </div>
                            </td>
                            <td class="py-4 fw-bolder fs-5 text-dark">45.00</td>
                            <td class="py-4"><span class="badge-status status-ada"><span class="text-success me-1">&bull;</span> ADA</span></td>
                        </tr>
                        <tr class="border-bottom border-light">
                            <td class="py-4 px-3 text-muted fw-bold">02</td>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://plus.unsplash.com/premium_photo-1664112065842-7a2e5d7102e1?auto=format&fit=crop&q=80&w=100" alt="Kangkung">
                                    <div class="fw-bold text-dark">Kangkung<br>Hidroponik</div>
                                </div>
                            </td>
                            <td class="py-4 fw-bolder fs-5 text-dark">12.50</td>
                            <td class="py-4"><span class="badge-status status-ada"><span class="text-success me-1">&bull;</span> ADA</span></td>
                        </tr>
                        <tr class="border-bottom border-light">
                            <td class="py-4 px-3 text-muted fw-bold">03</td>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&q=80&w=100" alt="Selada">
                                    <div>
                                        <div class="fw-bold text-dark">Selada<br>Keriting</div>
                                        <div class="text-danger fw-bold" style="font-size:0.6rem; letter-spacing:1px; margin-top:4px;">STOCK<br>DEFICIENCY<br>DETECTED</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 fw-bolder fs-5 text-danger">0.00</td>
                            <td class="py-4">
                                <span class="badge-status status-kosong"><span class="text-danger me-1">&bull;</span> KOSONG</span>
                                <span class="status-critical">CRITICAL</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-3 text-muted fw-bold">04</td>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://images.unsplash.com/photo-1603507024467-3cce7a05da93?auto=format&fit=crop&q=80&w=100" alt="Pakcoy">
                                    <div class="fw-bold text-dark">Pakcoy<br>Premium</div>
                                </div>
                            </td>
                            <td class="py-4 fw-bolder fs-5 text-dark">31.20</td>
                            <td class="py-4"><span class="badge-status status-ada"><span class="text-success me-1">&bull;</span> ADA</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <button class="btn btn-light w-100 fw-bold text-dark border-top pt-4 border-0 rounded-0" style="font-size:0.8rem; letter-spacing:1px;" onclick="alert('Buka Histori Semua Laci Database')">VIEW FULL ARCHIVE DATABASE <i class="fa-solid fa-angle-down ms-1 text-muted"></i></button>
        </div>
    </div>
</div>

<div class="row mt-1 mb-5 g-4">
    <div class="col-lg-6">
        <div class="input-card mt-3 border-0 py-4 pb-5 shadow-sm" style="background:#f7f9fa;">
            <div class="card-title-icon bg-white text-dark shadow-sm mb-3" style="width:36px; height:36px; font-size:1rem;"><i class="fa-solid fa-leaf"></i></div>
            <h6 class="fw-bold mb-2 text-dark">Sustainable Growth</h6>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Our greenhouse utilized 100% organic compost systems this harvest cycle.</p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="input-card mt-3 border-0 py-4 pb-5 shadow-sm" style="background:#e8ece2;">
            <h6 class="fw-bold mb-2 text-dark">Storage Utilization</h6>
            <p class="text-muted mb-4" style="font-size:0.85rem;">Capacity at 74%. Recommended harvest rotation by Friday evening.</p>
            <div class="d-flex justify-content-between text-dark fw-bold" style="font-size:0.75rem; letter-spacing:1px; margin-bottom:6px;">
                <span>USAGE</span><span>74%</span>
            </div>
            <div class="progress" style="height:8px; background:rgba(0,0,0,0.1); border-radius:10px;">
                <div class="progress-bar bg-dark rounded-pill" style="width:74%;"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function activateGrade(elem) {
        document.querySelectorAll('.grade-badge').forEach(e => e.classList.remove('active'));
        elem.classList.add('active');
    }
    
    function submitForm() {
        Swal.fire({
            title: 'Menyimpan Log Panen...',
            timer: 1500,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); }
        }).then(() => {
            Swal.fire('Catatan Disimpan!', 'Data bobot sayuran telah masuk ke buku besar inventaris.', 'success');
            document.querySelector('.yield-input-group input').value = '';
        });
    }
</script>
<?= $this->endSection() ?>
