<?php
session_start();         // Mulai sesi
session_unset();         // Hapus semua variabel sesi
session_destroy();       // Hancurkan sesi

// Arahkan ke halaman login
header("Location: login.php");
exit;
?>
