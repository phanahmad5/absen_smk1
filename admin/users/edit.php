<?php
session_start();
include '../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$id   = $_GET['id'];
$role = $_GET['role']; // Wali Kelas | Guru | Siswa

// Tentukan tabel berdasarkan role
if ($role == 'Wali Kelas') {
    $table = 'wali_kelas';
    $usernameField = 'username';
} elseif ($role == 'Guru') {
    $table = 'guru';
    $usernameField = 'username';
} elseif ($role == 'Siswa') {
    $table = 'siswa';
    $usernameField = 'nisn';
} else {
    die('Role tidak valid');
}

// Ambil data user
$user = $conn->query("SELECT * FROM $table WHERE id='$id'")->fetch_assoc();

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];

    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE $table SET nama='$nama', password='$pass' WHERE id='$id'");
    } else {
        $conn->query("UPDATE $table SET nama='$nama' WHERE id='$id'");
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Pengguna</title>
<link href="../../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">
<div class="card shadow">
<div class="card-body">

<h4>Edit <?= $role ?></h4>

<form method="POST">

<div class="form-group">
<label>Nama</label>
<input type="text" name="nama" class="form-control"
       value="<?= htmlspecialchars($user['nama']) ?>" required>
</div>

<div class="form-group">
<label>Username / NISN</label>
<input type="text" class="form-control"
       value="<?= htmlspecialchars($user[$usernameField]) ?>" readonly>
</div>

<div class="form-group">
<label>Password Baru (Opsional)</label>
<input type="password" name="password" class="form-control">
</div>

<button name="update" class="btn btn-primary">
<i class="fas fa-save"></i> Update
</button>

<a href="index.php" class="btn btn-secondary">Kembali</a>

</form>

</div>
</div>
</div>
</body>
</html>
