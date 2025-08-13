<?php
session_start();

// Fungsi untuk mengecek apakah user sudah login
function check_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit;
    }
}

// Fungsi untuk membatasi akses berdasarkan peran
function require_role($allowed_roles = []) {
    if (!isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], $allowed_roles)) {
        echo "<script>alert('Akses ditolak. Anda tidak memiliki izin.'); window.location='../logout.php';</script>";
        exit;
    }
}

// Fungsi untuk mendapatkan data user yang sedang login
function current_user() {
    return $_SESSION['user'] ?? null;
}

// Fungsi logout
function logout_user() {
    session_destroy();
    header("Location: ../login.php");
    exit;
}
?>
