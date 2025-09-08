<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    // Validasi hanya nilai enum yang diizinkan
    $allowed = ['Hadir', 'Alpha', 'Sakit', 'Izin'];
    if (!in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE absensi SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
}
