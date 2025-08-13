<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// 1. Koneksi database
include '../../config/koneksi.php';

// 2. Validasi dan ambil ID mapel
if (!isset($_GET['id'])) {
    header("Location: list_mapel.php");
    exit;
}

$id = (int)$_GET['id'];

// 3. Ambil data mapel berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM mapel WHERE id = $id");
$mapel = mysqli_fetch_assoc($result);

// 4. Jika data tidak ditemukan
if (!$mapel) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='list_mapel.php';</script>";
    exit;
}

// 5. Proses update saat form disubmit
if (isset($_POST['update'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_mapel']);

    mysqli_query($conn, "UPDATE mapel SET kode_mapel='$kode', nama_mapel='$nama' WHERE id=$id");

    header("Location: list_mapel.php");
    exit;
}

// 6. Include template header & sidebar
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Card Edit -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="m-0">Edit Mata Pelajaran</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="kode_mapel" class="form-label">Kode Mapel</label>
                            <input type="text" name="kode_mapel" id="kode_mapel" class="form-control" 
                                   value="<?= htmlspecialchars($mapel['kode_mapel']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_mapel" class="form-label">Nama Mapel</label>
                            <input type="text" name="nama_mapel" id="nama_mapel" class="form-control"
                                   value="<?= htmlspecialchars($mapel['nama_mapel']) ?>" required>
                        </div>
                        <button type="submit" name="update" class="btn btn-success">
                            <i class="fas fa-save"></i> Update
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
