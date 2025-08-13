<?php
include '../config/koneksi.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM guru WHERE id='$id'");

echo "<script>alert('Data guru berhasil dihapus');window.location='guru.php'</script>";
?>


