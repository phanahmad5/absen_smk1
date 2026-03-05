<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

$id_siswa = $_SESSION['user']['id'];

$query = $conn->query("
    SELECT nama, nisn, kelas, qr_code
    FROM siswa
    WHERE id = '$id_siswa'
    LIMIT 1
");

if ($query->num_rows == 0) {
    die('Data siswa tidak ditemukan');
}

$siswa = $query->fetch_assoc();

/* ===============================
   FIX PATH QR CODE
   =============================== */

// PATH ASLI FILE
$qrRelativePath = 'admin/siswa/' . $siswa['qr_code'];

// ENCODE SPASI PER FOLDER
$parts = explode('/', $qrRelativePath);
$encodedPath = implode('/', array_map('rawurlencode', $parts));

// BASE URL
$qrUrl = 'http://localhost/ABSENSI_SMK1KADUNGORA/' . $encodedPath;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR Code Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SB Admin 2 -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <?php include '../template/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <?php include '../template/topbar.php'; ?>

            <div class="container-fluid mt-4">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7">

                        <div class="card shadow text-center">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    QR Code Absensi Saya
                                </h6>
                            </div>

                            <div class="card-body">

                                <h5 class="font-weight-bold">
                                    <?= htmlspecialchars($siswa['nama']); ?>
                                </h5>
                                <p class="mb-1">NISN: <?= $siswa['nisn']; ?></p>
                                <p>Kelas: <?= $siswa['kelas']; ?></p>

                                <!-- QR CODE -->
                               <img
    src="<?= $qrUrl; ?>"
    alt="QR Code Siswa"
    class="img-fluid shadow border my-3"
    style="max-width:220px;"
>


                                <p class="text-muted small">
                                    Tunjukkan QR ini kepada petugas absensi
                                </p>

                                <a href="download_qr.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Download QR
                                </a>

                                <!-- DEBUG (boleh dihapus setelah normal) -->
                                <!--
                                <p class="small text-danger mt-2"><?= $qrUrl; ?></p>
                                -->

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <?php include '../template/footer.php'; ?>

    </div>
</div>

<!-- JS -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>
</html>
