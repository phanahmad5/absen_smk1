<?php
// ================= SESSION =================
session_start();
require '../../config/koneksi.php';

// ================= CEK LOGIN ADMIN =================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

// ================= SIMPAN DATA =================
if (isset($_POST['simpan'])) {

    $nama_kelas = trim(mysqli_real_escape_string($conn, $_POST['nama_kelas']));

    // Cek duplikat (case-insensitive)
    $cek = mysqli_query($conn, "
        SELECT id FROM kelas 
        WHERE LOWER(nama_kelas) = LOWER('$nama_kelas')
    ");

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Nama kelas sudah terdaftar!'
        ];

    } else {

        mysqli_query($conn, "
            INSERT INTO kelas (nama_kelas) 
            VALUES ('$nama_kelas')
        ");

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Data kelas berhasil ditambahkan.'
        ];

        header("Location: list_kelas.php");
        exit;
    }
}

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<div class="container-fluid mt-4">

<div class="card shadow">
<div class="card-header bg-primary text-white">
    <h5>Tambah Kelas</h5>
</div>

<div class="card-body">

<?php if (!empty($_SESSION['alert'])): ?>
<div class="alert alert-<?= $_SESSION['alert']['type']; ?>">
    <?= $_SESSION['alert']['message']; ?>
</div>
<?php unset($_SESSION['alert']); endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nama Kelas</label>
        <input type="text" name="nama_kelas" class="form-control" required autofocus>
    </div>

    <button type="submit" name="simpan" class="btn btn-success">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="list_kelas.php" class="btn btn-secondary">Kembali</a>
</form>

</div>
</div>

</div>
</div>

<?php include '../../template/footer.php'; ?>
</div>
