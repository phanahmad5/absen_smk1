<?php
session_start();
require_once '../config/koneksi.php';

// Cek role login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

$nisn = $_SESSION['user']['nisn'];

// Ambil data absensi siswa ini
$sql = "
    SELECT 
        a.id,
        a.nisn,
        s.nama AS nama_siswa,
        a.kelas,
        a.mata_pelajaran,
        a.tanggal,
        a.jam,
        a.status
    FROM absensi a
    JOIN siswa s ON a.nisn = s.nisn
    WHERE a.nisn = ?
    ORDER BY a.tanggal DESC, a.jam DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nisn);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <title>Absensi Saya</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <?php include '../template/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../template/topbar.php'; ?>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Absensi Saya</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Riwayat Absensi</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                                        <td><?= htmlspecialchars($row['kelas']); ?></td>
                                        <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                        <td><?= htmlspecialchars($row['jam']); ?></td>
                                        <td>
                                            <?php 
                                            if ($row['status'] === 'Hadir') {
                                                echo '<span class="badge badge-success">Hadir</span>';
                                            } elseif ($row['status'] === 'Sakit') {
                                                echo '<span class="badge badge-warning">Sakit</span>';
                                            } elseif ($row['status'] === 'Izin') {
                                                echo '<span class="badge badge-info">Izin</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Alpha</span>';
                                            }
                                            ?>
                                        </td>
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

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- JS -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "search": "Cari:"
            }
        });
    });
</script>

</body>
</html>
