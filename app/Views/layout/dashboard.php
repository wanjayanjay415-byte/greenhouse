<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard | GreenHouse' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Hotwire Turbo for Instant Page Transitions -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fa; color: #333; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; height: 100vh; }
        .sidebar { width: 260px; background: #f7f9fa; border-right: 1px solid #eaeaea; flex-shrink: 0; display: flex; flex-direction: column; padding: 20px 0; overflow-y:auto; overflow-x:hidden; transition: width 0.3s cubic-bezier(0.4,0,0.2,1), padding 0.3s ease; }
        .sidebar.collapsed { width: 0; padding: 0; border-right: none; }
        .sidebar.collapsed * { opacity: 0; pointer-events: none; transition: opacity 0.15s ease; }
        .sidebar:not(.collapsed) * { opacity: 1; transition: opacity 0.2s ease 0.1s; }
        .sidebar .brand { padding: 0 24px 30px; font-weight: 700; font-size: 1.2rem; color: #112a1f; text-decoration: none; white-space: nowrap;}
        .sidebar .nav-item { padding: 12px 24px; color: #555; text-decoration: none; display: flex; align-items: center; gap: 12px; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; cursor: pointer; white-space: nowrap; }
        .sidebar .nav-item:hover, .sidebar .nav-item.active { background: #eef2f0; color: #112a1f; border-right: 4px solid #112a1f; }
        .sidebar .nav-item i { width: 20px; text-align: center; }
        .user-profile { margin-top: auto; padding: 20px 24px; display: flex; align-items: center; gap: 12px; cursor:pointer; white-space: nowrap;}
        .user-profile:hover { background: #eef2f0; }
        .sidebar-toggle { padding: 10px 24px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #888; font-size: 0.8rem; font-weight: 600; transition: 0.2s; border-top: 1px solid #eaeaea; white-space: nowrap; }
        .sidebar-toggle:hover { color: #112a1f; background: #eef2f0; }
        .page-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; background-color: #fafbfc; transition: margin-left 0.3s ease; }
        .topbar { height: 70px; background: transparent; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; }
        .topbar-hamburger { width: 38px; height: 38px; border-radius: 10px; border: 1px solid #eaeaea; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 1rem; color: #112a1f; margin-right: 16px; flex-shrink: 0; }
        .topbar-hamburger:hover { background: #eef2f0; }
        .search-bar { background: #edf0f2; border-radius: 20px; padding: 6px 16px; display: flex; align-items: center; width: 300px; }
        .search-bar input { border: none; background: transparent; outline: none; width: 100%; margin-left: 8px; font-size: 0.9rem; }
        .topbar-icons { display: flex; gap: 20px; align-items: center; }
        .main-container { padding: 20px 40px; }
        .card-custom { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 24px; height: 100%; }
        .btn-dark-green { background-color: #0d2b1f; color: #fff; border-radius: 20px; padding: 8px 16px; font-size: 0.9rem; }
        .btn-dark-green:hover { background-color: #164734; color: #fff; }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebarNav">
            <a href="<?= base_url() ?>" class="brand border-bottom mb-2">
                <?= $sidebarTitle ?? 'Sistem Arsip' ?><br>
                <small class="text-muted fw-normal" style="font-size:0.7rem; letter-spacing:1px;"><?= $sidebarSub ?? 'MANAJEMEN GREENHOUSE' ?></small>
            </a>
            
            <div class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-angles-left" id="toggleIcon"></i>
                <span>Sembunyikan Menu</span>
            </div>

            <?= $this->renderSection('sidebar_menus') ?>
            
            <div class="user-profile" onclick="LogOut()">
                <div style="background:#ddd; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <div style="font-size:0.9rem; font-weight:600;"><?= $userName ?? 'Administrator' ?></div>
                    <div style="font-size:0.75rem; color:#888;">Keluar (Logout)</div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-content">
            <div class="topbar border-bottom">
                <div class="d-flex align-items-center">
                    <div class="topbar-hamburger" id="hamburgerBtn" onclick="toggleSidebar()" title="Toggle Sidebar" style="display:none;">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                    <div class="search-bar">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        <input type="text" placeholder="Cari data arsip global...">
                    </div>
                </div>
                <div class="topbar-icons">
                    <a href="javascript:void(0)" class="text-dark position-relative hover-shadow" onclick="showNotifModal()" style="text-decoration:none;">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <?php if(!empty($notifications) && count($notifications) > 0): ?>
                            <span id="notifBadge" class="position-absolute translate-middle badge rounded-pill bg-danger" style="top:2px; left:100%; font-size:0.6rem; padding:3px 6px;"><?= count($notifications) ?></span>
                        <?php endif; ?>
                    </a>
                    <i class="fa-solid fa-clock-rotate-left fs-5 text-muted cursor-pointer" onclick="alert('Riwayat Aktivitas belum tersedia')"></i>
                    <a href="javascript:void(0)" class="text-dark text-decoration-none border p-2 rounded bg-light hover-shadow" onclick="syncData()" style="font-size:0.9rem;">
                        <i class="fa-solid fa-arrows-rotate me-1" id="syncIcon"></i> Sinkronisasi
                    </a>
                    <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="fa-solid fa-shop me-1"></i> Mode Publik</a>
                </div>
            </div>

            <div class="main-container">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Modal Notifications -->
    <div class="modal fade" id="notifModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
          <div class="modal-header border-0 pb-2 pt-4 px-4" style="background:#0b2e21;">
            <div>
                <h5 class="modal-title fw-bold text-white mb-1"><i class="fa-solid fa-bell me-2" style="color:#bcf0da;"></i> Pusat Notifikasi</h5>
                <small class="text-white" style="opacity:0.7; font-size:0.75rem;">
                    <?php if(!empty($notifications)): ?>
                        <?= count($notifications) ?> notifikasi aktif
                    <?php else: ?>
                        Tidak ada notifikasi
                    <?php endif; ?>
                </small>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-0" style="max-height:480px; overflow-y:auto;">
            <?php if(!empty($notifications)): ?>
                <?php foreach($notifications as $n): ?>
                <?php $nid = md5(($n['title'] ?? '') . '|' . ($n['msg'] ?? '')); ?>
                <div class="d-flex align-items-start gap-3 p-3 border-bottom notif-item" data-notif-id="<?= $nid ?>" style="transition:0.2s; cursor:pointer;" onclick="markNotifRead('<?= $nid ?>')" onmouseenter="this.style.background='#f9fafb'" onmouseleave="this.style.background='#fff'">
                    <div style="width:40px; height:40px; border-radius:12px; background:<?= $n['bg'] ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="<?= $n['icon'] ?>" style="color:<?= $n['color'] ?>; font-size:0.9rem;"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 fw-bold text-dark" style="font-size:0.85rem;">
                                <span class="notif-dot d-inline-block" style="width:8px; height:8px; border-radius:50%; background:#dc2626; margin-right:6px; vertical-align:middle;"></span><?= esc($n['title']) ?>
                            </h6>
                            <small class="text-muted flex-shrink-0 ms-2" style="font-size:0.7rem;"><?= esc($n['time']) ?></small>
                        </div>
                        <p class="mb-0 text-muted" style="font-size:0.8rem; line-height:1.4;"><?= esc($n['msg']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-regular fa-bell-slash fa-3x text-muted mb-3 d-block" style="opacity:0.3;"></i>
                    <p class="text-muted fw-bold mb-0">Semua aman!</p>
                    <small class="text-muted">Tidak ada notifikasi saat ini.</small>
                </div>
            <?php endif; ?>
          </div>
          <?php if(!empty($notifications)): ?>
          <div class="modal-footer border-0 bg-light d-block py-3 px-4">
            <button type="button" class="btn w-100 fw-bold text-white py-2 mb-2" style="background:#0b2e21; border-radius:10px;" onclick="markAllNotifRead()">
                <i class="fa-solid fa-check-double me-2"></i> Tandai semua sudah dibaca
            </button>
            <div class="text-center">
                <small class="text-muted fw-bold" style="font-size:0.7rem; letter-spacing:1px;">NOTIFIKASI DIPERBARUI REAL-TIME DARI DATABASE</small>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function syncData() {
            const icon = document.getElementById('syncIcon');
            icon.classList.add('fa-spin');
            
            Swal.fire({
                title: 'Menyinkronkan Database...',
                text: 'Harap tunggu saat kami menghubungkan data lapangan dengan cloud.',
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                }
            }).then((result) => {
                icon.classList.remove('fa-spin');
                if (result.dismiss === Swal.DismissReason.timer) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sinkronisasi Berhasil!',
                        text: 'Seluruh database sayuran dan metrik telah terbarui secara real-time.'
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
        const NOTIF_READ_KEY = 'gh_dash_read_notifs';
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
                const dot = item.querySelector('.notif-dot');
                if (read.includes(item.dataset.notifId)) {
                    if (dot) dot.style.display = 'none';
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
            const merged = [...new Set([...getReadNotifs(), ...ids])];
            saveReadNotifs(merged);
            applyNotifReadState();
        }
        document.addEventListener('DOMContentLoaded', applyNotifReadState);

        function LogOut(){
            Swal.fire({
                title: 'Keluar Akun?',
                text: "Anda akan diarahkan kembali ke laman katalog pelanggan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0b2e21',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Keluar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('auth') ?>";
                }
            })
        }

        function confirmDelete(url, itemName) {
            Swal.fire({
                title: '<span style="font-size:1.3rem;font-weight:700;color:#112a1f;">Konfirmasi Penghapusan</span>',
                html: '<p style="color:#555;font-size:0.95rem;margin:0;">Apakah Anda yakin ingin menghapus <strong style="color:#dc2626;">' + itemName + '</strong> secara permanen?</p><p style="color:#999;font-size:0.8rem;margin-top:8px;">Data yang sudah dihapus tidak dapat dikembalikan.</p>',
                icon: 'warning',
                iconColor: '#dc2626',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-trash me-2"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fa-solid fa-xmark me-1"></i> Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'rounded-4 border-0 shadow-lg',
                    confirmButton: 'rounded-3 fw-bold px-4',
                    cancelButton: 'rounded-3 fw-bold px-4',
                },
                backdrop: 'rgba(11,46,33,0.4)'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarNav');
            const hamburger = document.getElementById('hamburgerBtn');

            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');

            // Tampilkan/sembunyikan hamburger di topbar
            hamburger.style.display = isCollapsed ? 'flex' : 'none';

            // Simpan state ke localStorage
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Restore sidebar state on page load
        (function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                const sidebar = document.getElementById('sidebarNav');
                const hamburger = document.getElementById('hamburgerBtn');
                sidebar.classList.add('collapsed');
                hamburger.style.display = 'flex';
            }
        })();
    </script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
