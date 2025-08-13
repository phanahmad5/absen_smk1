<?php
session_start();
require '../../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Simpan data kelas
if (isset($_POST['simpan'])) {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    mysqli_query($conn, "INSERT INTO kelas (nama_kelas) VALUES ('$nama_kelas')");
    header("Location: list_kelas.php");
    exit;
}

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">Tambah Kelas</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" required>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="list_kelas.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php include '../../template/footer.php'; ?>//
</div>
