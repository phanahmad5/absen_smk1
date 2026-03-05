<?php
include 'config/koneksi.php';

// Password default untuk semua siswa
$default_password = '12345';

// Hash password default (bcrypt)
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// Update password untuk siswa yang punya NISN
$sql = "UPDATE siswa SET password = ? WHERE nisn IS NOT NULL AND nisn != ''";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hashed_password);

if ($stmt->execute()) {
    echo "<h3 style='color:green'>✔ Semua password siswa berhasil diatur menjadi 12345</h3>";
    echo "<p>Password default siswa sekarang adalah: <b>12345</b></p>";
} else {
    echo "<h3 style='color:red'>❌ Gagal update password siswa:</h3> " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
