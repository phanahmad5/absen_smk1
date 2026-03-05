<?php
session_start();
include '../config/koneksi.php';

// Proteksi akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'walikelas') {
    header("Location: ../index.php?error=akses_ditolak");
    exit;
}

include '../template/header.php';
include '../template/sidebar.php';

// Ambil data user login
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM guru WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// Jika form disubmit
if (isset($_POST['update'])) {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare(
            "UPDATE guru SET nama = ?, username = ?, password = ? WHERE id = ?"
        );
        $update->bind_param("sssi", $nama, $username, $password_hash, $user_id);
    } else {
        $update = $conn->prepare(
            "UPDATE guru SET nama = ?, username = ? WHERE id = ?"
        );
        $update->bind_param("ssi", $nama, $username, $user_id);
    }

    if ($update->execute()) {
        // Update session
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['username'] = $username;

        echo "<script>
            alert('Profil berhasil diperbarui');
            window.location='profil.php';
        </script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

    <div class="row">
        <div class="col-lg-8">

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-user-edit mr-1"></i> Edit Profil Wali Kelas
                    </h6>
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
