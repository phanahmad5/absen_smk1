<?php
session_start();
include '../config/koneksi.php';
include '../template/header.php';
include '../template/sidebar.php';


// Ambil data user yang sedang login
$user_id = $_SESSION['user']['id'];
$query = mysqli_query($conn, "SELECT * FROM guru WHERE id = '$user_id'");
$data = mysqli_fetch_assoc($query);

// Jika form disubmit
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password_baru = $_POST['password'];

    // Update password jika diisi, jika tidak biarkan
    if (!empty($password_baru)) {
        $password_baru = md5($password_baru);
        $update = mysqli_query($conn, "UPDATE guru SET nama='$nama', username='$username', password='$password_baru' WHERE id='$user_id'");
    } else {
        $update = mysqli_query($conn, "UPDATE guru SET nama='$nama', username='$username' WHERE id='$user_id'");
    }

    if ($update) {
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['username'] = $username;
        echo "<script>alert('Profil berhasil diperbarui');window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

    <div class="row">
        <div class="col-lg-8">

            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Edit Profil</h6>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="<?= $data['nama'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?= $data['username'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru <small class="text-muted">(Kosongkan jika tidak diganti)</small></label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" name="update" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include '../template/footer.php'; ?>
