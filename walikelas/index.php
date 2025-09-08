<?php
session_start();
include '../config/koneksi.php';

// ============================
// Pastikan login wali kelas
// ============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'walikelas') {
    header("Location: ../index.php?error=akses_ditolak");
    exit;
}

// Ambil data dari session
$namaWali = $_SESSION['user']['nama'];
$kelas = $_SESSION['user']['kelas'] ?? '';

// ============================
// Hitung jumlah siswa di kelas
// ============================
$stmt = $conn->prepare("SELECT COUNT(*) AS total_siswa FROM siswa WHERE kelas = ?");
$stmt->bind_param("s", $kelas);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$totalSiswa = $row['total_siswa'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Wali Kelas</title>
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <?php include '../template/sidebar.php'; ?>
    <!-- End Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <?php include '../template/topbar.php'; ?>
            <!-- End Topbar -->

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Selamat Datang, <?= htmlspecialchars($namaWali) ?></h1>

                <div class="row">

                    <!-- Kartu Total Siswa -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body text-center">
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Jumlah Siswa di Kelas <?= htmlspecialchars($kelas) ?>
                                </div>
                                <div class="h1 mb-0 font-weight-bold text-gray-800"><?= $totalSiswa ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Cepat -->
                    <div class="col-xl-8 col-md-6 mb-4">
                        <div class="card shadow h-100 py-2">
                            <div class="card-body text-center">
                                <h5 class="font-weight-bold text-gray-800 mb-3">Menu Cepat</h5>
                                <a href="walikelas/absensi_kelas.php" class="btn btn-primary btn-lg mx-2">
                                    <i class="fas fa-users"></i> Lihat Absensi
                                </a>
                                <a href="rekap_absensi.php" class="btn btn-success btn-lg mx-2">
                                    <i class="fas fa-file-alt"></i> Rekap Absensi
                                </a>
                            </div>
                        </div>
                    </div>

                </div> <!-- End Row -->
            </div> <!-- End Container -->

        </div>
        <!-- Footer -->
        <?php include '../template/footer.php'; ?>
        <!-- End Footer -->
    </div>
</div>

<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
