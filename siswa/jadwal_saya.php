<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan role siswa
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

// Ambil data siswa yang login
$nisn = $_SESSION['user']['nisn'];
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nisn = ?");
$stmt->bind_param("s", $nisn);
$stmt->execute();
$siswaResult = $stmt->get_result();

if ($siswaResult->num_rows === 0) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.location='../logout.php';</script>";
    exit;
}
$siswa = $siswaResult->fetch_assoc();
$kelas = $siswa['kelas'];

// Mapping hari Inggris → Indonesia
$hari_map = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];

// Ambil jadwal siswa berdasarkan kelas
$stmt = $conn->prepare("
    SELECT j.*, m.nama_mapel, g.nama AS nama_guru
    FROM jadwal j
    JOIN mapel m ON j.mapel = m.id
    JOIN guru g ON j.id_guru = g.id
    WHERE j.kelas = ?
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
                             'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), 
             j.jam_mulai
");
$stmt->bind_param("s", $kelas);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
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
                <h1 class="h3 mb-4 text-gray-800">Jadwal Pelajaran Saya</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Kelas: <?= htmlspecialchars($kelas) ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($d = $result->fetch_assoc()):
                                        // Normalisasi nama hari
                                        $hari_db = $d['hari'];
                                        if (isset($hari_map[$hari_db])) {
                                            $jadwal_hari = $hari_map[$hari_db];
                                        } else {
                                            $jadwal_hari = ucfirst(strtolower($hari_db));
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($jadwal_hari) ?></td>
                                        <td><?= htmlspecialchars(date('H:i', strtotime($d['jam_mulai']))) ?> - <?= htmlspecialchars(date('H:i', strtotime($d['jam_selesai']))) ?></td>
                                        <td><?= htmlspecialchars($d['nama_mapel']) ?></td>
                                        <td><?= htmlspecialchars($d['nama_guru']) ?></td>
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
