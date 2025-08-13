<?php
session_start();
require_once '../config/koneksi.php';

// Cek login siswa
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

// Ambil data siswa dari sesi
$nama  = $_SESSION['user']['nama'] ?? '';
$kelas = $_SESSION['user']['kelas'] ?? '';

// Validasi kelas
if (empty($kelas)) {
    echo "<script>alert('Kelas tidak ditemukan di sesi!'); window.location='../logout.php';</script>";
    exit;
}

// Hari dan jam sekarang
date_default_timezone_set('Asia/Jakarta');
$hari_map = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];
$hari_ini = $hari_map[date('l')];
$jam_sekarang = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Saya</title>
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
                <h1 class="h3 mb-4 text-gray-800">Jadwal Saya (<?= htmlspecialchars($kelas) ?>)</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Daftar Jadwal</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mapel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT * FROM jadwal 
                                        WHERE kelas = ? 
                                        ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), jam_mulai
                                    ");
                                    $stmt->bind_param("s", $kelas);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $no = 1;
                                    if ($result->num_rows > 0):
                                        while ($d = $result->fetch_assoc()):
                                            $boleh_scan = (
                                                strtolower($d['hari']) === strtolower($hari_ini) &&
                                                $jam_sekarang >= $d['jam_mulai'] &&
                                                $jam_sekarang <= $d['jam_selesai']
                                            );
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($d['hari']) ?></td>
                                        <td><?= htmlspecialchars($d['jam_mulai']) ?> - <?= htmlspecialchars($d['jam_selesai']) ?></td>
                                        <td><?= htmlspecialchars($d['mapel']) ?></td>
                                        
                                    </tr>
                                    <?php
                                        endwhile;
                                    else:
                                        echo "<tr><td colspan='5' class='text-center'>Tidak ada jadwal ditemukan</td></tr>";
                                    endif;
                                    ?>
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

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada jadwal ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Data kosong",
                "search": "Cari:"
            }
        });
    });
</script>

</body>
</html>
