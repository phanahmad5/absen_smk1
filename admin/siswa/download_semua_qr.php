<?php
// Mulai session & include koneksi jika perlu
if (session_status() === PHP_SESSION_NONE) session_start();

// Folder QR Code
$qr_folder = 'qrcodes/';
$zip_filename = 'qr_codes.zip';

// Cek folder ada
if (!is_dir($qr_folder)) {
    die("Folder QR code tidak ditemukan.");
}

// Buat ZIP file sementara
$zip = new ZipArchive();
$temp_zip_path = tempnam(sys_get_temp_dir(), 'qrzip');

if ($zip->open($temp_zip_path, ZipArchive::CREATE) !== TRUE) {
    die("Gagal membuat file ZIP.");
}

// Tambahkan semua QR ke dalam ZIP
foreach (glob($qr_folder . "*.png") as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

// Kirim header agar browser download file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
header('Content-Length: ' . filesize($temp_zip_path));
readfile($temp_zip_path);

// Hapus file sementara
unlink($temp_zip_path);
exit;
