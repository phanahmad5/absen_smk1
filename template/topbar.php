<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" 
            class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Navbar Brand (opsional) -->
    <a class="navbar-brand d-sm-inline-block d-md-none font-weight-bold text-primary" 
       href="#">
        SMK 1 Kadungora
    </a>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto d-flex align-items-center">

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
               id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <!-- Nama user -->
                <span class="mr-2 d-none d-sm-inline text-gray-600 small text-truncate" 
                      style="max-width: 120px;">
                    <?= $_SESSION['user']['nama'] ?? 'Pengguna' ?>
                </span>

                <!-- Icon user -->
                <i class="fas fa-user-circle fa-lg text-primary"></i>
            </a>

            <!-- Dropdown -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profil.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil Saya
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Keluar
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->
