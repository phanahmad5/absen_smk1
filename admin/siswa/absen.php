<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Harus login sebagai siswa'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$kelas = $_GET['kelas'] ?? '';
$id_mapel = $_GET['id_mapel'] ?? '';
$token = $_GET['token'] ?? '';
$id_siswa = $_SESSION['user']['id'];

// Validasi token (aktif 1 jam terakhir)
$q = $conn->query("SELECT * FROM absensi_token 
                   WHERE token='$token' AND kelas='$kelas' AND id_mapel='$id_mapel' 
                   AND created_at >= NOW() - INTERVAL 1 HOUR");

if ($q->num_rows == 0) {
    echo "<script>alert('QR Code tidak valid atau sudah kadaluarsa'); window.location='dashboard.php';</script>";
    exit;
}

// Simpan absensi (hindari duplikat)
$conn->query("INSERT IGNORE INTO absensi (id_siswa, id_mapel, kelas, waktu) 
              VALUES ('$id_siswa','$id_mapel','$kelas',NOW())");

echo "<script>alert('Absensi berhasil dicatat!'); window.location='dashboard.php';</script>";
