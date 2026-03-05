<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require '../config/koneksi.php';

// ===============================
// VALIDASI LOGIN GURU
// ===============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    exit('Akses ditolak');
}

// ===============================
// AMBIL PARAMETER
// ===============================
$pertemuan_id = (int)($_GET['pertemuan_id'] ?? 0);
$nisn         = trim($_GET['nisn'] ?? '');

if ($pertemuan_id === 0 || $nisn === '') {
    exit('Data absensi tidak lengkap');
}

// ===============================
// AMBIL DATA PERTEMUAN + JADWAL
// ===============================
$stmt = $conn->prepare("
    SELECT 
        p.id AS pertemuan_id,
        p.tanggal,
        p.status,
        j.kelas,
        j.mapel AS mapel_id,
        m.nama_mapel,
        j.jam_mulai,
        j.jam_selesai
    FROM pertemuan p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN mapel m ON j.mapel = m.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $pertemuan_id);
$stmt->execute();
$pertemuan = $stmt->get_result()->fetch_assoc();

if (!$pertemuan) {
    exit('Pertemuan tidak ditemukan');
}

// ===============================
// VALIDASI PERTEMUAN AKTIF
// ===============================
$hariIni     = date('Y-m-d');
$jamSekarang = date('H:i:s');

if ($pertemuan['tanggal'] !== $hariIni || $pertemuan['status'] !== 'dibuka') {
    exit('Pertemuan tidak aktif');
}

$now   = strtotime($jamSekarang);
$start = strtotime($pertemuan['jam_mulai']) - (15 * 60);
$end   = strtotime($pertemuan['jam_selesai']) + (15 * 60);

if ($now < $start || $now > $end) {
    exit('Absensi di luar jam pertemuan');
}

// ===============================
// CEK SISWA + KELAS
// ===============================
$stmt = $conn->prepare("
    SELECT nama, kelas 
    FROM siswa 
    WHERE nisn = ?
");
$stmt->bind_param("s", $nisn);
$stmt->execute();
$resSiswa = $stmt->get_result();

if ($resSiswa->num_rows === 0) {
    exit("Siswa dengan NISN $nisn tidak ditemukan");
}

$siswa = $resSiswa->fetch_assoc();

// ===============================
// VALIDASI KELAS SISWA (INI KUNCI UTAMA 🔒)
// ===============================
if ($siswa['kelas'] !== $pertemuan['kelas']) {
    exit(
        "❌ Absensi ditolak!<br>
        Pertemuan ini untuk kelas <b>{$pertemuan['kelas']}</b>,<br>
        bukan kelas <b>{$siswa['kelas']}</b>."
    );
}

// ===============================
// CEK ABSENSI GANDA
// ===============================
$stmt = $conn->prepare("
    SELECT id FROM absensi 
    WHERE pertemuan_id = ? AND nisn = ?
");
$stmt->bind_param("is", $pertemuan_id, $nisn);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    exit("Halo {$siswa['nama']}, kamu sudah absen pada pertemuan ini");
}

// ===============================
// HITUNG STATUS (HADIR / TERLAMBAT)
// ===============================
$status = 'Hadir';
$selisihMenit = floor(($now - strtotime($pertemuan['jam_mulai'])) / 60);

if ($selisihMenit > 15) {
    $status = 'Terlambat';
}

// ===============================
// SIMPAN ABSENSI
// ===============================
$stmt = $conn->prepare("
    INSERT INTO absensi (
        pertemuan_id,
        nisn,
        mapel_id,
        mata_pelajaran,
        kelas,
        tanggal,
        jam,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isisssss",
    $pertemuan_id,
    $nisn,
    $pertemuan['mapel_id'],
    $pertemuan['nama_mapel'],
    $pertemuan['kelas'],
    $hariIni,
    $jamSekarang,
    $status
);

if ($stmt->execute()) {
    if ($status === 'Terlambat') {
        echo "⚠️ {$siswa['nama']} tercatat TERLAMBAT pada {$pertemuan['nama_mapel']}";
    } else {
        echo "✅ Absensi berhasil! Selamat datang {$siswa['nama']}";
    }
} else {
    echo "❌ Gagal menyimpan absensi";
}
