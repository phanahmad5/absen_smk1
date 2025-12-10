<?php
session_start();
require_once '../config/koneksi.php';
include '../vendor/phpqrcode-master/qrlib.php'; // bukan autoload composer

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$kelas = $_GET['kelas'] ?? '';
$id_mapel = $_GET['id_mapel'] ?? '';

if (!$kelas || !$id_mapel) {
    die("Data tidak valid");
}

// Buat token unik
$token = bin2hex(random_bytes(8));

// Hapus token lama
$conn->query("DELETE FROM absensi_token WHERE kelas='$kelas' AND id_mapel='$id_mapel'");
$conn->query("INSERT INTO absensi_token (kelas, id_mapel, token, created_at) 
              VALUES ('$kelas','$id_mapel','$token',NOW())");

// URL yang akan discan siswa
$base_url = " https://af79225093d7.ngrok-free.app/absensi_smk1kadungora"; 
$url = $base_url . "/admin/siswa/absen.php?kelas=$kelas&id_mapel=$id_mapel&token=$token";

// Buat QR ke file sementara
$filename = 'qr_temp.png';
QRcode::png($url, $filename, QR_ECLEVEL_L, 6);

// Convert ke base64 biar bisa ditampilkan di <img>
$imageData = base64_encode(file_get_contents($filename));
unlink($filename); // hapus file setelah dipakai
?>
<img src="data:image/png;base64,<?= $imageData ?>" alt="QR Code Absensi">
