<?php
session_start();
include '../../config/koneksi.php';

/* ===============================
   🔒 CEK LOGIN & ROLE ADMIN
=============================== */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

/* ===============================
   🔎 VALIDASI ID
=============================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID guru tidak valid!'); window.location='guru.php';</script>";
    exit;
}

$id = intval($_GET['id']);

/* ===============================
   🔍 CEK DATA GURU
=============================== */
$stmt = $conn->prepare("SELECT id FROM guru WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<script>alert('Data guru tidak ditemukan!'); window.location='guru.php';</script>";
    exit;
}

/* ===============================
   🧹 HAPUS DATA WALI KELAS (JIKA ADA)
=============================== */
$stmt = $conn->prepare("DELETE FROM wali_kelas WHERE guru_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

/* ===============================
   🗑️ HAPUS DATA GURU
=============================== */
$stmt = $conn->prepare("DELETE FROM guru WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        alert('Data guru berhasil dihapus');
        window.location='guru.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus data guru');
        window.location='guru.php';
    </script>";
}
?>
