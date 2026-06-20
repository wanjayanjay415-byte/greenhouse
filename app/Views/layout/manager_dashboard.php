<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Greenhouse Admin Panel' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fc; color: #333; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; height: 100vh; }
        
        .sidebar { width: 280px; background: #fdfdfd; flex-shrink: 0; display: flex; flex-direction: column; overflow-y:auto; border-right: 1px solid #f0f2f5; }
        .sidebar-brand-box { padding: 40px 30px; }
        .sidebar .brand { font-weight: 800; font-size: 1.4rem; color: #112a1f; text-decoration: none; line-height: 1.2;}
        
        .sidebar-menu-list { padding: 0 16px; flex-grow: 1;}
        .sidebar .nav-item { padding: 14px 24px; color: #6c757d; text-decoration: none; display: flex; align-items: center; gap: 16px; font-weight: 600; font-size: 0.95rem; border-radius: 12px; margin-bottom: 4px; transition: all 0.2s; cursor: pointer; }
        .sidebar .nav-item:hover { color: #0b2e21; background: #f7f9fa; }
        .sidebar .nav-item.active { background: #e8f4ed; color: #0b2e21; box-shadow: inset 0 0 0 1px #d1e8d9;}
        .sidebar .nav-item i { width: 20px; text-align: center; font-size:1.1rem; }
        
        .user-profile-box { margin: 20px 16px 30px; padding: 16px; border-radius: 16px; background: #f0f2f5; display: flex; align-items: center; gap: 12px; cursor:pointer; transition:0.2s; border:1px solid transparent;}
        .user-profile-box:hover { border-color:#dcdcdc; background:#e9ebef;}
        .user-profile-box img { width: 42px; height: 42px; border-radius: 50%; object-fit:cover;}
        
        .page-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; background-color: rgba(247,249,252,0.6); }
        
        .topbar { height: 80px; background: transparent; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; border-bottom: 1px solid #f0f2f5;}
        
        .topbar-brand { font-weight: 800; font-size: 1.1rem; letter-spacing: 2px; color: #112a1f; text-transform: uppercase;}
        
        .topbar-links { display: flex; align-items: center; gap: 30px; height: 100%;}
        .topbar-link { text-decoration: none; color: #6c757d; font-weight: 600; font-size: 0.95rem; height: 100%; display: flex; align-items: center; position: relative; transition:0.2s;}
        .topbar-link:hover { color: #112a1f; }
        .topbar-link.active { color: #112a1f; }
        .topbar-link.active::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #112a1f; border-radius: 3px 3px 0 0;}
        
        .topbar-icons { display: flex; gap: 20px; align-items: center; }
        .icon-btn { color: #555; font-size: 1.2rem; cursor: pointer; transition: 0.2s; text-decoration: none;}
        .icon-btn:hover { color: #112a1f; }
        
        .btn-sync { background-color: #0b2e21; color: #fff; border-radius: 30px; padding: 10px 24px; font-size: 0.85rem; font-weight: 700; border:none; transition:0.2s;}
        .btn-sync:hover { background-color: #164734; }
 
        .main-container { padding: 40px; max-width: 1300px;}
        
        .card-custom { background: #fff; border-radius: 20px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); padding: 30px; height: 100%; }
        
        /* Helpers */
        .cursor-pointer { cursor: pointer; }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-brand-box">
                <a href="<?= base_url('manager') ?>" class="brand">
                    GreenHouse<br>
                    <small class="text-muted fw-bold" style="font-size:0.6rem; letter-spacing:1.5px; opacity:0.8; text-transform:uppercase;">Admin & Manager Panel</small>
                </a>
            </div>
            
            <div class="sidebar-menu-list">
                <?= $this->renderSection('sidebar_menus') ?>
            </div>
            
            <div class="user-profile-box" onclick="LogOut()">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Admin Profile">
                <div>
                    <div style="font-size:0.9rem; font-weight:800; color:#112a1f;">Administrator</div>
                    <div style="font-size:0.75rem; color:#888; font-weight:600;"><?= esc(session()->get('role') ?? 'Admin') ?></div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-content">
            <div class="topbar">
                <div class="topbar-brand">GreenHouse</div>
                
                <div class="topbar-links">
                    <a href="javascript:void(0)" class="topbar-link active">Admin Dashboard</a>
                </div>
                
                <div class="topbar-icons">
                    <a href="javascript:void(0)" class="icon-btn"><i class="fa-solid fa-gear"></i></a>
                    <a href="javascript:void(0)" class="icon-btn position-relative" onclick="showNotifModal()">
                        <i class="fa-regular fa-bell"></i>
                        <span id="notifBadge" class="position-absolute translate-middle badge rounded-pill bg-danger" style="top:5px; padding:3px 5px; font-size:0.55rem; left: 18px; display:none;">0</span>
                    </a>
                    <a href="javascript:void(0)" class="icon-btn"><i class="fa-regular fa-user"></i></a>
                    
                    <button class="btn-sync ms-2" onclick="syncData()"><i class="fa-solid fa-arrows-rotate me-2" id="syncIcon"></i> Sync Inventory</button>
                </div>
            </div>

            <div class="main-container">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Modal Notifications -->
    <div class="modal fade" id="notifModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold">Live Alerts</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php $notifs = $notifications ?? []; ?>
            <?php if (empty($notifs)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fa-regular fa-bell-slash fs-2 mb-2 d-block opacity-50"></i>
                    Tidak ada notifikasi saat ini.
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush mt-1" id="notifList">
                    <?php foreach ($notifs as $n): ?>
                        <?php $nid = md5(($n['title'] ?? '') . '|' . ($n['msg'] ?? '')); ?>
                        <a href="#" class="list-group-item list-group-item-action notif-item py-3 px-2 border-bottom"
                           data-notif-id="<?= $nid ?>" onclick="markNotifRead('<?= $nid ?>'); return false;">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <h6 class="mb-1 fw-bold" style="color:<?= esc($n['color'] ?? '#333', 'attr') ?>;">
                                    <i class="<?= esc($n['icon'] ?? 'fa-solid fa-bell', 'attr') ?> me-2"></i><?= esc($n['title'] ?? '') ?>
                                    <span class="notif-dot d-inline-block" style="width:8px; height:8px; border-radius:50%; background:#dc2626; margin-left:6px;"></span>
                                </h6>
                                <small class="text-muted text-nowrap ms-2"><?= esc($n['time'] ?? '') ?></small>
                            </div>
                            <p class="mb-0 text-muted small ms-4"><?= esc($n['msg'] ?? '') ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($notifs)): ?>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn w-100 fw-bold text-white py-2" style="background:#0b2e21; border-radius:10px;" onclick="markAllNotifRead()">
                <i class="fa-solid fa-check-double me-2"></i> Tandai semua sudah dibaca
            </button>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function syncData() {
            const icon = document.getElementById('syncIcon');
            icon.classList.add('fa-spin');
            
            Swal.fire({
                title: 'Synchronizing Inventory...',
                text: 'Deploying deep scan to all connected archivers.',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                }
            }).then((result) => {
                icon.classList.remove('fa-spin');
                if (result.dismiss === Swal.DismissReason.timer) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Synced!',
                        text: 'Live inventory ledgers are up to date.',
                        confirmButtonColor: '#0b2e21'
                    });
                }
            })
        }

        function showNotifModal(){
            new bootstrap.Modal(document.getElementById('notifModal')).show();
            // Membuka lonceng = notifikasi dianggap sudah dilihat → angka badge hilang
            markAllNotifRead();
        }

        // ===== Sistem Read Notifikasi (persist di localStorage) =====
        const NOTIF_READ_KEY = 'gh_mgr_read_notifs';
        function getReadNotifs(){
            try { return JSON.parse(localStorage.getItem(NOTIF_READ_KEY) || '[]'); }
            catch(e){ return []; }
        }
        function saveReadNotifs(arr){
            localStorage.setItem(NOTIF_READ_KEY, JSON.stringify(arr));
        }
        function applyNotifReadState(){
            const read = getReadNotifs();
            let unread = 0;
            document.querySelectorAll('.notif-item').forEach(item => {
                const id  = item.dataset.notifId;
                const dot = item.querySelector('.notif-dot');
                if (read.includes(id)) {
                    if (dot) dot.style.display = 'none';   // hilangkan titik merah "belum dibaca"
                } else {
                    if (dot) dot.style.display = 'inline-block';
                    unread++;
                }
            });
            const badge = document.getElementById('notifBadge');
            if (badge) {
                if (unread > 0) { badge.textContent = unread; badge.style.display = 'inline-block'; }
                else { badge.style.display = 'none'; }
            }
        }
        function markNotifRead(id){
            const read = getReadNotifs();
            if (!read.includes(id)) { read.push(id); saveReadNotifs(read); }
            applyNotifReadState();
        }
        function markAllNotifRead(){
            const ids = [...document.querySelectorAll('.notif-item')].map(i => i.dataset.notifId);
            // Gabungkan dengan yang sudah ada agar tidak menghapus riwayat lama
            const merged = [...new Set([...getReadNotifs(), ...ids])];
            saveReadNotifs(merged);
            applyNotifReadState();
        }
        document.addEventListener('DOMContentLoaded', applyNotifReadState);

        function LogOut(){
            Swal.fire({
                title: 'Sign Out?',
                text: "Secure your session before leaving the archive.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0b2e21',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Sign Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('auth') ?>";
                }
            })
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
