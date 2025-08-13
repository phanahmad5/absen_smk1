<?php
session_start();
require '../config/koneksi.php';

// Cek login & role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil nama user
$nama_user = $_SESSION['user']['nama'];

// Hitung jumlah data
$total_siswa  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa"))['total'];
$total_guru   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM guru"))['total'];
$total_mapel  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM mapel"))['total'];
$total_jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal"))['total'];

// Load template
include '../template/header.php';
include '../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
 <?php include '../template/topbar.php'; ?>
        <div class="container-fluid mt-4">
            <!-- Judul Halaman -->
            <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>
            <p>Selamat datang, <strong><?= htmlspecialchars($nama_user); ?></strong>!</p>

            <!-- Row Cards -->
            <div class="row">

                <!-- Card Total Siswa -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_siswa; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Guru -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_guru; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Mapel -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Mapel</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_mapel; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Jadwal -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Jadwal</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_jadwal; ?></div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Row -->
        </div>

    </div>
    <!-- End Content -->

    <?php include '../template/footer.php'; ?>
</div>
<!-- End Content Wrapper -->

</div> <!-- End Wrapper -->
