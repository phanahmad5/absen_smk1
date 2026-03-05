<?php
session_start();
require_once '../config/koneksi.php';

// ===============================
// VALIDASI ADMIN
// ===============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}


$akademik_active = true;
$submenu = 'tahun_ajaran';

// ===============================
// SIMPAN TAHUN AJARAN
// ===============================
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);

    if ($nama !== '') {
        $stmt = $conn->prepare("INSERT INTO tahun_ajaran (nama, status) VALUES (?, 'nonaktif')");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
    }
    header("Location: tahun_ajaran.php");
    exit;
}

// ===============================
// AKTIFKAN TAHUN AJARAN
// ===============================
if (isset($_GET['aktifkan'])) {
    $id = (int)$_GET['aktifkan'];

    $conn->query("UPDATE tahun_ajaran SET status='nonaktif'");
    $conn->query("UPDATE tahun_ajaran SET status='aktif' WHERE id=$id");

    header("Location: tahun_ajaran.php");
    exit;
}

// ===============================
// AMBIL DATA
// ===============================
$data = $conn->query("SELECT * FROM tahun_ajaran ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tahun Ajaran</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

<?php include '../template/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php include '../template/topbar.php'; ?>

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Tahun Ajaran</h1>

<div class="card shadow mb-4">
<div class="card-body">

<form method="post" class="form-inline mb-3">
    <input type="text" name="nama" class="form-control mr-2" placeholder="Contoh: 2025/2026" required>
    <button name="simpan" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah
    </button>
</form>

<table class="table table-bordered">
<thead>
<tr>
    <th>No</th>
    <th>Tahun Ajaran</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($r=$data->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td>
        <span class="badge badge-<?= $r['status']=='aktif'?'success':'secondary' ?>">
            <?= ucfirst($r['status']) ?>
        </span>
    </td>
    <td>
        <?php if ($r['status']!='aktif'): ?>
            <a href="?aktifkan=<?= $r['id'] ?>" class="btn btn-success btn-sm">
                Aktifkan
            </a>
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

<?php include '../template/footer.php'; ?>
</div>
</div>

</body>
</html>
