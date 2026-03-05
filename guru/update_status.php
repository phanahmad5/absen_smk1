<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    exit('unauthorized');
}

$id     = $_POST['id'];
$status = $_POST['status'];

$allowed = ['Hadir','Sakit','Izin','Alpha'];
if (!in_array($status, $allowed)) {
    exit('invalid');
}

$stmt = $conn->prepare("UPDATE absensi SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

echo $stmt->execute() ? 'success' : 'error';
