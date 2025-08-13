<?php
session_start();
require '../../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

$id = intval($_GET['id']); // pastikan integer

// Ambil nama_kelas dari ID kelas
$get_kelas = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id = $id");
if (mysqli_num_rows($get_kelas) === 0) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Kelas tidak ditemukan.'
    ];
    header("Location: list_kelas.php");
    exit;
}
$data_kelas = mysqli_fetch_assoc($get_kelas);
$nama_kelas = mysqli_real_escape_string($conn, $data_kelas['nama_kelas']);

// Cek apakah nama_kelas ini digunakan di tabel siswa
$cek_siswa = mysqli_query($conn, "SELECT 1 FROM siswa WHERE kelas = '$nama_kelas' LIMIT 1");

// Cek apakah nama_kelas ini digunakan di tabel jadwal
$cek_jadwal = mysqli_query($conn, "SELECT 1 FROM jadwal WHERE kelas = '$nama_kelas' LIMIT 1");

if (mysqli_num_rows($cek_siswa) > 0 || mysqli_num_rows($cek_jadwal) > 0) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Kelas tidak bisa dihapus karena masih digunakan di data siswa atau jadwal.'
    ];
    header("Location: list_kelas.php");
    exit;
}

// Jika aman, hapus kelas
$hapus = mysqli_query($conn, "DELETE FROM kelas WHERE id = $id");

if ($hapus) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Kelas berhasil dihapus.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal menghapus kelas.'
    ];
}

header("Location: list_kelas.php");
exit;
