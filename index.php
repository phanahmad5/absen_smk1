<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
} else {
    switch ($_SESSION['user']['role']) {

        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'guru':
            header("Location: guru/dashboard.php");
            break;
        case 'siswa':
            header("Location: siswa/dashboard.php");
            break;
    }
}
?>