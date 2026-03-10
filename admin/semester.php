<?php
session_start();
require_once '../config/koneksi.php';


// admin validasi
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}


// SIMPAN SEMESTER

if (isset($_POST['simpan'])) {
    $tahun = (int)$_POST['tahun_ajaran_id'];
    $nama  = $_POST['nama'];

    if ($tahun && $nama) {
        $stmt = $conn->prepare("
            INSERT INTO semester (tahun_ajaran_id, nama, status)
            VALUES (?, ?, 'nonaktif')
        ");
        $stmt->bind_param("is", $tahun, $nama);
        $stmt->execute();
    }
    header("Location: semester.php");
    exit;
}


// AKTIFKAN SEMESTER

if (isset($_GET['aktifkan'])) {
    $id = (int)$_GET['aktifkan'];

    $conn->query("UPDATE semester SET status='nonaktif'");
    $conn->query("UPDATE semester SET status='aktif' WHERE id=$id");

    header("Location: semester.php");
    exit;
}


// DATA

$tahunAjaran = $conn->query("SELECT * FROM tahun_ajaran");
$semester = $conn->query("
    SELECT s.*, t.nama AS tahun 
    FROM semester s
    JOIN tahun_ajaran t ON s.tahun_ajaran_id=t.id
    ORDER BY s.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semester</title>
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

<h1 class="h3 mb-4 text-gray-800">Semester</h1>

<div class="card shadow mb-4">
<div class="card-body">

<form method="post" class="form-inline mb-3">
    <select name="tahun_ajaran_id" class="form-control mr-2" required>
        <option value="">Pilih Tahun Ajaran</option>
        <?php while($t=$tahunAjaran->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>">
                <?= $t['nama'] ?> (<?= $t['status'] ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <select name="nama" class="form-control mr-2" required>
        <option value="">Pilih Semester</option>
        <option value="Ganjil">Ganjil</option>
        <option value="Genap">Genap</option>
    </select>

    <button name="simpan" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah
    </button>
</form>

<table class="table table-bordered">
<thead>
<tr>
    <th>No</th>
    <th>Tahun Ajaran</th>
    <th>Semester</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($s=$semester->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($s['tahun']) ?></td>
    <td><?= htmlspecialchars($s['nama']) ?></td>
    <td>
        <span class="badge badge-<?= $s['status']=='aktif'?'success':'secondary' ?>">
            <?= ucfirst($s['status']) ?>
        </span>
    </td>
    <td>
        <?php if ($s['status']!='aktif'): ?>
            <a href="?aktifkan=<?= $s['id'] ?>" class="btn btn-success btn-sm">
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
