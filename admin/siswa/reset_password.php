<?php
session_start();
include '../../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$password_baru = password_hash("12345", PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare("UPDATE siswa SET password=? WHERE id=?");
$stmt->bind_param('si', $password_baru, $id);

if ($stmt->execute()) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Password siswa berhasil direset menjadi 12345.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Gagal mereset password siswa.'
    ];
}

$stmt->close();
$conn->close();
header("Location: index.php");
exit;
?>
