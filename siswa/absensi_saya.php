<?php
session_start();
require_once '../config/koneksi.php';

/* ===============================
   VALIDASI LOGIN SISWA (AMAN)
=============================== */
if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'siswa' ||
    !isset($_SESSION['user']['nisn'])
) {
    echo "<script>
        alert('Session tidak valid, silakan login ulang');
        window.location='../login.php';
    </script>";
    exit;
}

$nisn  = $_SESSION['user']['nisn'];
$nama  = $_SESSION['user']['nama'];
$kelas = $_SESSION['user']['kelas'];

/* ===============================
   AMBIL DATA ABSENSI SISWA
=============================== */
$sql = "
    SELECT 
        a.id,
        a.mata_pelajaran,
        a.tanggal,
        a.jam,
        a.status
    FROM absensi a
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
    <title>Absensi Saya</title>
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">

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

<h1 class="h3 mb-2 text-gray-800">Absensi Saya</h1>
<p class="mb-4 text-muted">
    Nama: <strong><?= htmlspecialchars($nama) ?></strong> |
    Kelas: <strong><?= htmlspecialchars($kelas) ?></strong>
</p>

<div class="card shadow mb-4">
<div class="card-header py-3 bg-info text-white">
    <h6 class="m-0 font-weight-bold">Riwayat Absensi</h6>
</div>

<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered table-hover" id="dataTable">
<thead class="thead-light">
<tr>
    <th>No</th>
    <th>Mata Pelajaran</th>
    <th>Tanggal</th>
    <th>Jam</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php if ($result->num_rows === 0): ?>
<tr>
    <td colspan="5" class="text-center text-muted">
        Belum ada data absensi
    </td>
</tr>
<?php else: ?>

<?php $no = 1; while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
    <td><?= htmlspecialchars($row['jam']) ?></td>
    <td>
        <?php
        switch ($row['status']) {
            case 'Hadir':
                echo '<span class="badge badge-success">Hadir</span>';
                break;
            case 'Sakit':
                echo '<span class="badge badge-warning">Sakit</span>';
                break;
            case 'Izin':
                echo '<span class="badge badge-info">Izin</span>';
                break;
            default:
                echo '<span class="badge badge-danger">Alpha</span>';
        }
        ?>
    </td>
</tr>
<?php endwhile; ?>

<?php endif; ?>

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
        pageLength: 10,
        order: [[2, 'desc']],
        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Belum ada data",
            search: "Cari:"
        }
    });
});
</script>

</body>
</html>
