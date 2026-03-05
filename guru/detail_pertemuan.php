<?php
session_start();
require_once '../config/koneksi.php';

// ===============================
// VALIDASI LOGIN GURU
// ===============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

// ===============================
// VALIDASI PARAMETER
// ===============================
$pertemuan_id = (int)($_GET['pertemuan_id'] ?? 0);
if ($pertemuan_id === 0) {
    exit('Pertemuan tidak valid');
}

$id_guru = $_SESSION['user']['id'];

// ===============================
// AMBIL DETAIL PERTEMUAN
// ===============================
$stmt = $conn->prepare("
    SELECT 
        p.id AS pertemuan_id,
        p.tanggal,
        p.status,
        p.jadwal_id,
        j.kelas,
        j.jam_mulai,
        j.jam_selesai,
        m.nama_mapel
    FROM pertemuan p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN mapel m ON j.mapel = m.id
    WHERE p.id = ? AND j.id_guru = ?
");
$stmt->bind_param("ii", $pertemuan_id, $id_guru);
$stmt->execute();
$pertemuan = $stmt->get_result()->fetch_assoc();

if (!$pertemuan) {
    exit('Data pertemuan tidak ditemukan');
}

// ===============================
// HITUNG PERTEMUAN KE-
// ===============================
$stmtKe = $conn->prepare("
    SELECT COUNT(*) AS pertemuan_ke
    FROM pertemuan
    WHERE jadwal_id = ?
      AND tanggal <= ?
");
$stmtKe->bind_param(
    "is",
    $pertemuan['jadwal_id'],
    $pertemuan['tanggal']
);
$stmtKe->execute();
$pertemuan_ke = $stmtKe->get_result()->fetch_assoc()['pertemuan_ke'];

// ===============================
// AMBIL DATA ABSENSI
// ===============================
$absen = $conn->prepare("
    SELECT 
        a.nisn,
        s.nama,
        a.jam,
        a.status
    FROM absensi a
    JOIN siswa s ON a.nisn = s.nisn
    WHERE a.pertemuan_id = ?
    ORDER BY s.nama
");
$absen->bind_param("i", $pertemuan_id);
$absen->execute();
$data_absen = $absen->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pertemuan</title>

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

<h1 class="h3 mb-4 text-gray-800">Detail Pertemuan</h1>

<!-- INFO PERTEMUAN -->
<div class="card shadow mb-4">
<div class="card-body">

<table class="table table-borderless mb-0">
<tr>
    <th width="180">Mata Pelajaran</th>
    <td>: <?= htmlspecialchars($pertemuan['nama_mapel']) ?></td>
</tr>
<tr>
    <th>Kelas</th>
    <td>: <?= htmlspecialchars($pertemuan['kelas']) ?></td>
</tr>
<tr>
    <th>Tanggal</th>
    <td>: <?= date('d-m-Y', strtotime($pertemuan['tanggal'])) ?></td>
</tr>
<tr>
    <th>Pertemuan</th>
    <td>:
        <span class="badge badge-info">
            Pertemuan ke-<?= $pertemuan_ke ?>
        </span>
    </td>
</tr>
<tr>
    <th>Jam</th>
    <td>: <?= htmlspecialchars($pertemuan['jam_mulai']) ?> - <?= htmlspecialchars($pertemuan['jam_selesai']) ?></td>
</tr>
<tr>
    <th>Status</th>
    <td>:
        <span class="badge badge-<?= $pertemuan['status'] === 'dibuka' ? 'success' : 'secondary' ?>">
            <?= ucfirst($pertemuan['status']) ?>
        </span>
    </td>
</tr>
</table>

</div>
</div>

<!-- TABEL ABSENSI -->
<div class="card shadow mb-4">
<div class="card-header py-3 bg-primary text-white">
    <h6 class="m-0 font-weight-bold">Daftar Kehadiran Siswa</h6>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered" id="dataTable">
<thead class="thead-light">
<tr>
    <th>No</th>
    <th>NISN</th>
    <th>Nama Siswa</th>
    <th>Jam Absen</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php if ($data_absen->num_rows === 0): ?>
<tr>
    <td colspan="5" class="text-center text-muted">
        Belum ada siswa yang melakukan absensi
    </td>
</tr>
<?php else: ?>
<?php $no = 1; while ($row = $data_absen->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['nisn']) ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td><?= htmlspecialchars($row['jam']) ?></td>
    <td>
        <span class="badge badge-success">
            <?= htmlspecialchars($row['status']) ?>
        </span>
    </td>
</tr>
<?php endwhile; ?>
<?php endif; ?>

</tbody>
</table>
</div>
</div>
</div>

<a href="jadwal.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Kembali
</a>

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
