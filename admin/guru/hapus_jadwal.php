<?php
include '../../config/koneksi.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM jadwal WHERE id='$id'");

echo "<script>alert('Data jadwal berhasil dihapus');window.location='jadwal.php'</script>";
?>
