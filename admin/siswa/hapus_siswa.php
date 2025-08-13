<?php
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

// Include koneksi
include '../../config/koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Cek apakah data siswa ada
    $cek = $conn->query("SELECT * FROM siswa WHERE id = $id");
    if ($cek->num_rows > 0) {

        // Hapus data siswa
        $hapus = $conn->query("DELETE FROM siswa WHERE id = $id");

        if ($hapus) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data siswa berhasil dihapus.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Gagal menghapus data siswa.'
            ];
        }

    } else {
        $_SESSION['alert'] = [
            'type' => 'warning',
            'message' => 'Data siswa tidak ditemukan.'
        ];
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'ID siswa tidak valid.'
    ];
}

// Redirect kembali ke index
header('Location: index.php');
exit;
?>
