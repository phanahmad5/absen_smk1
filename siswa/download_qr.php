<?php
session_start();
include '../config/koneksi.php';

// cek login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    die('Akses ditolak');
}

$id_siswa = $_SESSION['user']['id'];

// AMBIL DATA QR SISWA

$query = $conn->query("
    SELECT nama, nisn, qr_code
    FROM siswa
    WHERE id = '$id_siswa'
    LIMIT 1
");

if ($query->num_rows == 0) {
    die('Data siswa tidak ditemukan');
}

$siswa = $query->fetch_assoc();


// Lokasi file QR
$filePath = realpath(__DIR__ . '/../admin/siswa/' . $siswa['qr_code']);

if (!$filePath || !file_exists($filePath)) {
    die('File QR Code tidak ditemukan');
}

// ===============================
// FORCE DOWNLOAD
// ===============================
$filename = 'QR_' . $siswa['nisn'] . '.png';

header('Content-Description: File Transfer');
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filePath));
header('Pragma: public');
header('Cache-Control: must-revalidate');
header('Expires: 0');

readfile($filePath);
exit;
