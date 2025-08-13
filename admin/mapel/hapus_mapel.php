<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Koneksi database
include '../../config/koneksi.php';

// Validasi ID dari parameter GET
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Hapus data mapel berdasarkan id
    mysqli_query($conn, "DELETE FROM mapel WHERE id = $id");
}

// Redirect ke halaman list mapel
header("Location: list_mapel.php");
exit;
?>
