<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    exit('Akses ditolak');
}

$jadwal_id = (int)($_GET['jadwal_id'] ?? 0);
if ($jadwal_id === 0) exit('Jadwal tidak valid');

// Cegah dobel
$cek = $conn->prepare("
    SELECT id FROM pertemuan 
    WHERE jadwal_id = ? AND tanggal = CURDATE()
");
$cek->bind_param("i", $jadwal_id);
$cek->execute();
if ($cek->get_result()->num_rows > 0) {
    header("Location: jadwal.php");
    exit;
}

// hitung pertemuan ke
$q = $conn->prepare("SELECT COUNT(*) total FROM pertemuan WHERE jadwal_id = ?");
$q->bind_param("i", $jadwal_id);
$q->execute();
$ke = $q->get_result()->fetch_assoc()['total'] + 1;

// insert
$stmt = $conn->prepare("
    INSERT INTO pertemuan (jadwal_id, tanggal, pertemuan_ke)
    VALUES (?, CURDATE(), ?)
");
$stmt->bind_param("ii", $jadwal_id, $ke);
$stmt->execute();

header("Location: jadwal.php");
