<?php
session_start();
include '../../config/koneksi.php';

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Cek apakah data wali kelas ada
$cek = $conn->query("SELECT * FROM wali_kelas WHERE id = $id");

if ($cek->num_rows == 0) {
    echo "<script>alert('Data wali kelas tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Proses hapus data
$hapus = $conn->query("DELETE FROM wali_kelas WHERE id = $id");

if ($hapus) {
    echo "<script>alert('Data wali kelas berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data!'); window.location='index.php';</script>";
}
?>
