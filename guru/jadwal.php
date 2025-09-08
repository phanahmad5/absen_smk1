<?php
session_start();
require_once '../config/koneksi.php';

// Cek role login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

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

$hari_ini = $hari_map[date('l')]; // contoh: "Rabu"
$jam_sekarang = date('H:i');

$id_guru = $_SESSION['user']['id'];

// Ambil jadwal guru dengan join mapel
$stmt = $conn->prepare("
    SELECT j.id, j.hari, j.jam_mulai, j.jam_selesai, j.kelas, j.mapel AS id_mapel, m.nama_mapel
    FROM jadwal j
    JOIN mapel m ON j.mapel = m.id
    WHERE j.id_guru = ?
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
                             'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), 
             j.jam_mulai
");
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <title>Jadwal Mengajar</title>
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
                <h1 class="h3 mb-4 text-gray-800">Jadwal Mengajar</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Data Jadwal Anda</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($d = $result->fetch_assoc()):
                                        // Normalisasi nama hari dari database
                                        $hari_db = $d['hari'];
                                        if (isset($hari_map[$hari_db])) {
                                            // Hari dalam bahasa Inggris → ubah ke Indonesia
                                            $jadwal_hari = $hari_map[$hari_db];
                                        } else {
                                            // Asumsi hari sudah bahasa Indonesia di DB
                                            $jadwal_hari = ucfirst(strtolower($hari_db));
                                        }

                                        $jam_mulai = $d['jam_mulai'];
                                        $jam_selesai = $d['jam_selesai'];

                                        // Gunakan strtotime untuk perbandingan jam + toleransi waktu
                                        $now = strtotime($jam_sekarang);
                                        $start = strtotime($jam_mulai . ' -5 minutes'); // 15 menit sebelum mulai
                                        $end = strtotime($jam_selesai . ' +5 minutes'); // 15 menit sesudah selesai

                                        // Cek apakah sekarang masuk jadwal
                                        $boleh_absen = (
                                            strtolower($jadwal_hari) === strtolower($hari_ini) &&
                                            $now >= $start &&
                                            $now <= $end
                                        );
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($jadwal_hari) ?></td>
                                        <td><?= htmlspecialchars($jam_mulai) ?> - <?= htmlspecialchars($jam_selesai) ?></td>
                                        <td><?= htmlspecialchars($d['kelas']) ?></td>
                                        <td><?= htmlspecialchars($d['nama_mapel']) ?></td>
                                        <td>
                                            <?php if ($boleh_absen): ?>
                                                <a href="scan.php?kelas=<?= urlencode($d['kelas']) ?>&id_mapel=<?= urlencode($d['id_mapel']) ?>&nama_mapel=<?= urlencode($d['nama_mapel']) ?>" 
                                                   class="btn btn-info btn-sm mb-1">
                                                    <i class="fas fa-qrcode"></i> Absen
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm mb-1" disabled>
                                                    <i class="fas fa-lock"></i> Di luar jadwal
                                                </button>
                                            <?php endif; ?>

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
