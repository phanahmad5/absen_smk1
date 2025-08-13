<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    foreach ($_POST['status'] as $id => $status) {
        $stmt = $conn->prepare("UPDATE absensi SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }

    // Alert sukses dan kembali ke halaman daftar absensi
    echo "<script>
        alert('Perubahan status absensi berhasil disimpan!');
        window.location.href = 'lihat_absensi.php';
    </script>";
} else {
    echo "<script>
        alert('Tidak ada perubahan yang disimpan.');
        window.location.href = 'absensi.php';
    </script>";
}
