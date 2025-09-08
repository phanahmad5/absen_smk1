<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (!$user || !isset($user['role'])) {
    echo '<div class="text-danger p-3">Akses ditolak. Silakan login terlebih dahulu.</div>';
    exit;
}

// Penanda menu aktif
$dashboard_active = $dashboard_active ?? false;
$siswa_active     = $siswa_active ?? false;
$guru_active      = $guru_active ?? false;
$mapel_active     = $mapel_active ?? false;
$jadwal_active    = $jadwal_active ?? false;
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-user"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Absensi SMK</div>
    </a>

    <hr class="sidebar-divider my-0">

    <div class="sidebar-heading">Menu Navigasi</div>

    <?php if ($user['role'] === 'admin'): ?>

        <!-- Dashboard -->
        <li class="nav-item <?= $dashboard_active ? 'active' : '' ?>">
            <a class="nav-link" href="/absensi_smk1kadungora/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Data Siswa -->
        <li class="nav-item <?= $siswa_active ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="javascript:void(0)" 
               data-toggle="collapse" data-target="#siswaMenu" 
               aria-expanded="<?= $siswa_active ? 'true' : 'false' ?>" aria-controls="siswaMenu">
                <i class="fas fa-user-graduate"></i>
                <span>Data Siswa</span>
            </a>
            <div id="siswaMenu" class="collapse <?= $siswa_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'siswa_tambah' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/siswa/tambah.php">Input Siswa</a>
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'siswa_view' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/siswa/index.php">Lihat Siswa</a>
                </div>
            </div>
        </li>

        <!-- Data Guru -->
        <li class="nav-item <?= $guru_active ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="javascript:void(0)" 
               data-toggle="collapse" data-target="#guruMenu" 
               aria-expanded="<?= $guru_active ? 'true' : 'false' ?>" aria-controls="guruMenu">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Data Guru</span>
            </a>
            <div id="guruMenu" class="collapse <?= $guru_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'guru_tambah' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/guru/tambah_guru.php">Input Guru</a>
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'guru_view' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/guru/guru.php">Lihat Guru</a>
                </div>
            </div>
        </li>
        <!-- Data Kelas -->
        <li class="nav-item <?= $guru_active ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="javascript:void(0)" 
               data-toggle="collapse" data-target="#kelasMenu" 
               aria-expanded="<?= $kelas_active ? 'true' : 'false' ?>" aria-controls="kelasMenu">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Data Kelas</span>
            </a>
            <div id="kelasMenu" class="collapse <?= $kelas_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'kelas_tambah' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/kelas/tambah_kelas.php">Input Kelas</a>
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'guru_view' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/kelas/list_kelas.php">Lihat Kelas</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?= $kelas_active ? 'active' : '' ?>">
        <a class="nav-link collapsed" href="javascript:void(0)" 
           data-toggle="collapse" data-target="#waliKelasMenu" 
           aria-expanded="<?= $kelas_active ? 'true' : 'false' ?>" aria-controls="waliKelasMenu">
            <i class="fas fa-users"></i>
            <span>Data Walikelas</span>
        </a>
        <div id="waliKelasMenu" class="collapse <?= $kelas_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?= isset($submenu) && $submenu == 'absensi_view' ? 'active' : '' ?>" 
                   href="/absensi_smk1kadungora/admin/walikelas/tambah.php">Input Walikelas</a>
                <a class="collapse-item <?= isset($submenu) && $submenu == 'rekap_absensi' ? 'active' : '' ?>" 
                   href="/absensi_smk1kadungora/admin/walikelas/index.php">Lihat Walikelas</a>
            </div>
        </div>
    </li>

        <!-- Data Mapel -->
        <li class="nav-item <?= $mapel_active ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="javascript:void(0)" 
               data-toggle="collapse" data-target="#mapelMenu" 
               aria-expanded="<?= $mapel_active ? 'true' : 'false' ?>" aria-controls="mapelMenu">
                <i class="fas fa-book"></i>
                <span>Data Mapel</span>
            </a>
            <div id="mapelMenu" class="collapse <?= $mapel_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'mapel_tambah' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/mapel/tambah_mapel.php">Input Mapel</a>
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'mapel_view' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/mapel/list_mapel.php">Lihat Mapel</a>
                </div>
            </div>
        </li>

        <!-- Data Jadwal -->
        <li class="nav-item <?= $jadwal_active ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="javascript:void(0)" 
               data-toggle="collapse" data-target="#jadwalMenu" 
               aria-expanded="<?= $jadwal_active ? 'true' : 'false' ?>" aria-controls="jadwalMenu">
                <i class="fas fa-calendar-alt"></i>
                <span>Data Jadwal</span>
            </a>
            <div id="jadwalMenu" class="collapse <?= $jadwal_active ? 'show' : '' ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'jadwal_tambah' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/guru/tambah_jadwal.php">Input Data</a>
                    <a class="collapse-item <?= isset($submenu) && $submenu == 'jadwal_view' ? 'active' : '' ?>" 
                       href="/absensi_smk1kadungora/admin/guru/jadwal.php">Lihat Data</a>
                </div>
            </div>
        </li>

    <?php elseif ($user['role'] === 'guru'): ?>

        <li class="nav-item <?= $dashboard_active ? 'active' : '' ?>">
            <a class="nav-link" href="/absensi_smk1kadungora/guru/dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/absensi_smk1kadungora/guru/jadwal.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Mengajar</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/absensi_smk1kadungora/guru/lihat_absensi.php">
                <i class="fas fa-clipboard-list"></i>
                <span>Lihat absen</span>
            </a>
        </li>

        

    <?php elseif ($user['role'] === 'siswa'): ?>

        <li class="nav-item <?= $dashboard_active ? 'active' : '' ?>">
            <a class="nav-link" href="/absensi_smk1kadungora/siswa/index.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/absensi_smk1kadungora/siswa/jadwal_saya.php">
                <i class="fas fa-calendar-check"></i>
                <span>Jadwal Pelajaran</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/absensi_smk1kadungora/siswa/absensi_saya.php">
                <i class="fas fa-list"></i>
                <span>Absensi Saya</span>
            </a>
        </li>


    <?php elseif ($user['role'] === 'walikelas'): ?>

    <li class="nav-item <?= $dashboard_active ? 'active' : '' ?>">
        <a class="nav-link" href="/absensi_smk1kadungora/walikelas/index.php">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="/absensi_smk1kadungora/walikelas/absensi_kelas.php">
            <i class="fas fa-users"></i>
            <span>Daftar Siswa</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="/absensi_smk1kadungora/walikelas/rekap_absensi.php">
            <i class="fas fa-file-alt"></i>
            <span>Rekap Absensi</span>
        </a>
    </li>

<?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="/absensi_smk1kadungora/logout.php" onclick="return confirm('Yakin ingin logout?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </li>

</ul>
<!-- End of Sidebar -->

    <!-- Script Bootstrap -->
    <script src="/absensi_smk1kadungora/vendor/jquery/jquery.min.js"></script>
    <script src="/absensi_smk1kadungora/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
