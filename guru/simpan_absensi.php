<?php
date_default_timezone_set('Asia/Jakarta');
include '../config/koneksi.php';

// Ambil NISN dari GET
$nisn = trim($_GET['nisn'] ?? '');

// Validasi NISN
if ($nisn === '') {
    echo "<script>alert('NISN tidak ditemukan di QR Code!');</script>";
    exit;
}

// Cek siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nisn = ?");
$stmt->bind_param("s", $nisn);
$stmt->execute();
$siswaResult = $stmt->get_result();

if ($siswaResult->num_rows === 0) {
    echo "<script>alert('Siswa dengan NISN $nisn tidak ditemukan!');</script>";
    exit;
}
$siswa = $siswaResult->fetch_assoc();

// Tentukan hari sekarang (Bahasa Indonesia)
$hariInggris = date('l');
$hariMap = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
];
$hari = $hariMap[$hariInggris] ?? $hariInggris;

// Cari jadwal sesuai hari & kelas siswa
$stmt = $conn->prepare("
    SELECT j.*, m.id AS mapel_id, m.nama_mapel 
    FROM jadwal j
    JOIN mapel m ON j.mapel = m.id
    WHERE j.kelas = ? 
      AND j.hari = ? 
      AND TIME(NOW()) BETWEEN j.jam_mulai AND j.jam_selesai
    LIMIT 1
");
$stmt->bind_param("ss", $siswa['kelas'], $hari);
$stmt->execute();
$jadwalResult = $stmt->get_result();

if ($jadwalResult->num_rows === 0) {
    echo "<script>alert('Tidak ada jadwal pada jam ini untuk kelas {$siswa['kelas']}!');</script>";
    exit;
}
$jadwal = $jadwalResult->fetch_assoc();

$mapel_id = $jadwal['mapel_id'];
$mapel = $jadwal['nama_mapel'];
$kelas = $siswa['kelas'];

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
    echo "<script>alert('Halo {$siswa['nama']}, kamu sudah absen hari ini untuk mata pelajaran $mapel!');</script>";
    exit;
}

// Hitung status hadir/terlambat dengan aman (tanpa pengaruh detik)
$jamSekarang = date('H:i:s');
$waktuMulai  = strtotime($jadwal['jam_mulai']);
$waktuScan   = strtotime($jamSekarang);
$selisihMenit = floor(($waktuScan - $waktuMulai) / 60);

$status = ($selisihMenit > 15) ? 'Terlambat' : 'Hadir';

// Simpan absensi
$stmt = $conn->prepare("
    INSERT INTO absensi (nisn, mapel_id, mata_pelajaran, kelas, tanggal, jam, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sisssss", $nisn, $mapel_id, $mapel, $kelas, $tanggal, $jamSekarang, $status);

if ($stmt->execute()) {
    $pesan = ($status === 'Terlambat')
        ? "Maaf {$siswa['nama']}, kamu terlambat untuk pelajaran $mapel."
        : "Absensi berhasil, {$siswa['nama']}! Selamat datang di pelajaran $mapel.";
    echo "<script>alert('$pesan');</script>";
} else {
    echo "<script>alert('Gagal menyimpan absensi!');</script>";
}
?>
