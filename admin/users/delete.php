<?php
session_start();
include '../../config/koneksi.php';

// hanya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$id   = $_GET['id'];
$role = $_GET['role']; // Wali Kelas | Guru | Siswa

// Tentukan tabel berdasarkan role
if ($role == 'Wali Kelas') {
    $table = 'wali_kelas';
} elseif ($role == 'Guru') {
    $table = 'guru';
} elseif ($role == 'Siswa') {
    $table = 'siswa';
} else {
    die('Role tidak valid');
}

// Hapus data
$query = $conn->query("DELETE FROM $table WHERE id='$id'");

if ($query) {
    header("Location: index.php?hapus=success");
} else {
    header("Location: index.php?hapus=gagal");
}
exit;
