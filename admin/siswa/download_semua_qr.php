<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';

// Folder tempat QR disimpan
$qr_folder = __DIR__ . "/../../qrcodes/";

// Nama file zip sementara
$zip_filename = "semua_qr_codes.zip";
$zip = new ZipArchive();
$temp_zip_path = tempnam(sys_get_temp_dir(), 'qrzip');

if ($zip->open($temp_zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Gagal membuat file ZIP.");
}

// Ambil semua QR Code dari database siswa
$sql = $conn->query("SELECT qr_code, nisn, nama FROM siswa");
while ($row = $sql->fetch_assoc()) {
    $qr_path = $row['qr_code'];

    // Pastikan path valid (bisa relatif/absolut)
    if (file_exists($qr_path)) {
        // Nama file di ZIP → format: NISN_Nama.png
        $nama_file = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['nisn'] . "_" . $row['nama']) . ".png";
        $zip->addFile($qr_path, $nama_file);
    }
}

$zip->close();

// Kirim file ZIP ke browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
header('Content-Length: ' . filesize($temp_zip_path));
readfile($temp_zip_path);

// Hapus file sementara
unlink($temp_zip_path);
exit;
