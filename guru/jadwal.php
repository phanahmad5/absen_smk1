<?php
session_start();
require_once '../config/koneksi.php';

// Cek login guru
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

$hari_ini = $hari_map[date('l')];
$jam_sekarang = date('H:i:s');

$id_guru = $_SESSION['user']['id'];

// Ambil jadwal guru
$stmt = $conn->prepare("
    SELECT 
        j.id AS jadwal_id,
        j.hari,
        j.jam_mulai,
        j.jam_selesai,
        j.kelas,
        j.mapel AS mapel_id,
        m.nama_mapel
    FROM jadwal j
    JOIN mapel m ON j.mapel = m.id
    WHERE j.id_guru = ?
    ORDER BY 
        FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),
        j.jam_mulai
");
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
<table class="table table-bordered" id="dataTable" width="100%">
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

    // Normalisasi hari dari DB
    $jadwal_hari = ucfirst(strtolower($d['hari']));

    // Cek apakah sekarang dalam jadwal (±15 menit)
    $now   = strtotime($jam_sekarang);
    $start = strtotime($d['jam_mulai']) - (15 * 60);
    $end   = strtotime($d['jam_selesai']) + (15 * 60);

    $dalam_jadwal = (
        strtolower($jadwal_hari) === strtolower($hari_ini) &&
        $now >= $start &&
        $now <= $end
    );

    // Cek pertemuan hari ini
    $cek = $conn->prepare("
        SELECT id FROM pertemuan 
        WHERE jadwal_id = ? AND tanggal = CURDATE()
    ");
    $cek->bind_param("i", $d['jadwal_id']);
    $cek->execute();
    $pertemuan = $cek->get_result()->fetch_assoc();
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($jadwal_hari) ?></td>
    <td><?= htmlspecialchars($d['jam_mulai']) ?> - <?= htmlspecialchars($d['jam_selesai']) ?></td>
    <td><?= htmlspecialchars($d['kelas']) ?></td>
    <td><?= htmlspecialchars($d['nama_mapel']) ?></td>
    <td>

    <?php if (!$dalam_jadwal): ?>
        <button class="btn btn-secondary btn-sm" disabled>
            <i class="fas fa-lock"></i> Di luar jadwal
        </button>

    <?php elseif (!$pertemuan): ?>
        <a href="buka_pertemuan.php?jadwal_id=<?= $d['jadwal_id'] ?>"
           class="btn btn-success btn-sm">
            <i class="fas fa-play"></i> Buka Pertemuan
        </a>

    <?php else: ?>
        <a href="scan.php?pertemuan_id=<?= $pertemuan['id'] ?>"
           class="btn btn-info btn-sm">
            <i class="fas fa-qrcode"></i> Scan Absen
        </a>
    <?php endif; ?>

    <div class="btn-group">
    
    <a href="detail_pertemuan.php?pertemuan_id=<?= $pertemuan['id'] ?>"
       class="btn btn-warning btn-sm">
        <i class="fas fa-eye"></i>
    </a>
</div>



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

<!-- JS -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>
$(document).ready(function () {
    $('#dataTable').DataTable({
        pageLength: 10,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            zeroRecords: "Data tidak ditemukan"
        }
    });
});
</script>

</body>
</html>
