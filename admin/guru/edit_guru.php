<?php
include '../../config/koneksi.php';

// ambil data guru berdasarkan ID
$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM guru WHERE id='$id'");
$g = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];

    // update password jika diisi
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE guru SET nama='$nama', nip='$nip', username='$username', password='$password' WHERE id='$id'";
    } else {
        $query = "UPDATE guru SET nama='$nama', nip='$nip', username='$username' WHERE id='$id'";
    }

    mysqli_query($conn, $query);
    echo "<script>alert('Data guru berhasil diperbarui');window.location='guru.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Guru - Absensi</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include '../../template/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include '../../template/topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Edit Data Guru</h1>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Guru</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <label>Nama Guru</label>
                                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($g['nama']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($g['nip']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($g['username']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password <small>(kosongkan jika tidak diganti)</small></label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        <button type="submit" name="update" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        <a href="guru.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include '../../template/footer.php'; ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Scripts -->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
</body>

</html>
