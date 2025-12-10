<?php
// Jalankan dulu koneksi ke database
include 'config/koneksi.php';

// Password default yang akan digunakan untuk semua siswa
$default_password = '12345';

// Hash password menggunakan bcrypt
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// Query untuk update semua password siswa
$sql = "UPDATE siswa SET password = ?";

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hashed_password);

if ($stmt->execute()) {
    echo "<h3 style='color: green;'>✅ Semua password siswa berhasil diperbarui!</h3>";
    echo "<p>Password default sekarang adalah: <b>12345</b></p>";
} else {
    echo "<h3 style='color: red;'>❌ Gagal memperbarui password siswa:</h3> " . $conn->error;
}

$stmt->close();
$conn->close();
?>
