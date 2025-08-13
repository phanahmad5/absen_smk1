<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// 1. include koneksi database
include '../../config/koneksi.php';

// 2. Proses simpan data (dijalankan sebelum ada output HTML)
if (isset($_POST['simpan'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_mapel']);

    mysqli_query($conn, "INSERT INTO mapel (kode_mapel, nama_mapel) VALUES ('$kode', '$nama')");

    // redirect sebelum ada output apapun
    header("Location: list_mapel.php");
    exit;
}

// 3. Include template (header & sidebar)
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Card Form -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">Tambah Mata Pelajaran</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="kode_mapel" class="form-label">Kode Mapel</label>
                            <input type="text" name="kode_mapel" id="kode_mapel" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_mapel" class="form-label">Nama Mapel</label>
                            <input type="text" name="nama_mapel" id="nama_mapel" class="form-control" required>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="list_mapel.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
            <!-- End Card -->

        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End Content Wrapper -->

</div> <!-- End #wrapper -->
