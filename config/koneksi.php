<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_absensi_smk';

$conn = mysqli_connect($host, $user, $pass,$db);

if ($conn->connect_error) {
    die("connection failed: " .$conn->connect_error);
}
?>