<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$nama     = $_SESSION['user']['nama'];
$nisn     = $_SESSION['user']['nisn'];
$kelas    = $_SESSION['user']['kelas'];
$id       = $_SESSION['user']['id'];

$msg = "";
$err = "";

// Proses update password
if (isset($_POST['update_password'])) {
    $new_pass = trim($_POST['new_password']);
    $confirm  = trim($_POST['confirm_password']);

    if ($new_pass === "" || $confirm === "") {
        $err = "Password baru tidak boleh kosong.";
    } elseif ($new_pass !== $confirm) {
        $err = "Konfirmasi password tidak cocok.";
    } else {
        // Simpan password baru (pakai hash biar aman)
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE siswa SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $id);
        if ($stmt->execute()) {
            $msg = "Password berhasil diperbarui!";
        } else {
            $err = "Gagal memperbarui password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil Siswa</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include '../template/sidebar.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <?php include '../template/topbar.php'; ?>
            <!-- Header -->
            <?php include '../template/header.php'; ?>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Profil Siswa</h1>

                <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php elseif ($err): ?>
                    <div class="alert alert-danger"><?= $err ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <table class="table table-bordered mb-4">
                            <tr>
                                <th>Nama</th>
                                <td><?= htmlspecialchars($nama) ?></td>
                            </tr>
                            <tr>
                                <th>NISN</th>
                                <td><?= htmlspecialchars($nisn) ?></td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td><?= htmlspecialchars($kelas) ?></td>
                            </tr>
                        </table>

                        <h5>Update Password</h5>
                        <form method="POST">
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
        
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php include '../template/footer.php'; ?>
        <!-- End of Footer -->
    </div>

</div>
<!-- End of Page Wrapper -->

<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
