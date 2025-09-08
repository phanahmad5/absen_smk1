<?php
date_default_timezone_set('Asia/Jakarta');
include '../config/koneksi.php';

// Ambil parameter dari GET
$nisn       = trim($_GET['nisn'] ?? '');
$kelas      = trim($_GET['kelas'] ?? '');
$mapel_id   = (int) ($_GET['id_mapel'] ?? 0);
$mapel      = trim($_GET['mapel_nama'] ?? '');

// Validasi
if ($nisn === '' || $kelas === '' || $mapel_id === 0 || $mapel === '') {
    echo "Data absensi tidak lengkap!";
    exit;
}

// Cek siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nisn = ?");
$stmt->bind_param("s", $nisn);
$stmt->execute();
$siswaResult = $stmt->get_result();

if ($siswaResult->num_rows === 0) {
    echo "Siswa dengan NISN $nisn tidak ditemukan!";
    exit;
}
$siswa = $siswaResult->fetch_assoc();

// Cek absensi sebelumnya
$tanggal = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT * FROM absensi 
    WHERE nisn = ? AND tanggal = ? AND mapel_id = ?
");
$stmt->bind_param("ssi", $nisn, $tanggal, $mapel_id);
$stmt->execute();
$cekResult = $stmt->get_result();

if ($cekResult->num_rows > 0) {
    echo "Halo {$siswa['nama']}, kamu sudah absen hari ini untuk mata pelajaran $mapel!";
    exit;
}

// Hitung status hadir/terlambat
$jamSekarang = date('H:i:s');

// Ambil jam mulai mapel dari tabel jadwal (optional, supaya lebih akurat)
$stmt = $conn->prepare("
    SELECT jam_mulai 
    FROM jadwal 
    WHERE kelas = ? AND mapel = ? 
    ORDER BY jam_mulai ASC LIMIT 1
");
$stmt->bind_param("si", $kelas, $mapel_id);
$stmt->execute();
$resJadwal = $stmt->get_result();
$jadwal = $resJadwal->fetch_assoc();

$status = "Hadir";
if ($jadwal) {
    $waktuMulai  = strtotime($jadwal['jam_mulai']);
    $waktuScan   = strtotime($jamSekarang);
    $selisihMenit = floor(($waktuScan - $waktuMulai) / 60);
    if ($selisihMenit > 15) {
        $status = "Terlambat";
    }
}

// Simpan absensi
$stmt = $conn->prepare("
    INSERT INTO absensi (nisn, mapel_id, mata_pelajaran, kelas, tanggal, jam, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sisssss", $nisn, $mapel_id, $mapel, $kelas, $tanggal, $jamSekarang, $status);

if ($stmt->execute()) {
    if ($status === 'Terlambat') {
        echo "Maaf {$siswa['nama']}, kamu terlambat untuk pelajaran $mapel.";
    } else {
        echo "Absensi berhasil, {$siswa['nama']}! Selamat datang di pelajaran $mapel.";
    }
} else {
    echo "Gagal menyimpan absensi!";
}
?>
