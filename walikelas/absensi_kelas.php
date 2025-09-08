<?php
session_start();
include '../config/koneksi.php';

// Pastikan login wali kelas
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'walikelas') {
    header("Location: ../index.php?error=akses_ditolak");
    exit;
}

$kelas = $_SESSION['user']['kelas'];

// Ambil nama wali kelas dari tabel wali_kelas
$stmtWali = $conn->prepare("SELECT nama FROM wali_kelas WHERE kelas = ? LIMIT 1");
$stmtWali->bind_param("s", $kelas);
$stmtWali->execute();
$resWali = $stmtWali->get_result();
$wali = $resWali->fetch_assoc();
$namaWali = $wali ? $wali['nama'] : "-";

// Ambil daftar siswa kelas
$stmt = $conn->prepare("SELECT * FROM siswa WHERE kelas = ? ORDER BY nama ASC");
$stmt->bind_param("s", $kelas);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa Kelas <?= htmlspecialchars($kelas) ?></title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="../vendor/jquery/jquery.min.js"></script>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include '../template/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../template/topbar.php'; ?>
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Daftar Siswa Kelas <?= htmlspecialchars($kelas) ?></h1>
                <p>Wali Kelas: <strong><?= htmlspecialchars($namaWali) ?></strong></p>

                <!-- Tabel daftar siswa -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">Data Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>TTL</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No Telp</th>
                
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['nisn']); ?></td>
                                        <td><?= htmlspecialchars($row['nama']); ?></td>
                                        <td><?= htmlspecialchars($row['ttl']); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jk']); ?></td>
                                        <td><?= htmlspecialchars($row['no_telp']); ?></td>
                                       
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 10,
        "language": {
            "search": "Cari:",
            "zeroRecords": "Data tidak ditemukan"
        }
    });
});
</script>
</body>
</html>
