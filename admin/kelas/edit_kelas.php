<?php
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

require '../../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Ambil ID kelas dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'ID kelas tidak valid.'
    ];
    header("Location: list_kelas.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data kelas
$query = mysqli_query($conn, "SELECT * FROM kelas WHERE id = $id");
if (!$query || mysqli_num_rows($query) == 0) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Data kelas tidak ditemukan.'
    ];
    header("Location: list_kelas.php");
    exit;
}

$kelas = mysqli_fetch_assoc($query);

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = trim($_POST['nama_kelas']);

    if ($nama_kelas === '') {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Nama kelas tidak boleh kosong.'
        ];
    } else {
        $update = mysqli_query($conn, "UPDATE kelas SET nama_kelas = '".mysqli_real_escape_string($conn, $nama_kelas)."' WHERE id = $id");

        if ($update) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Data kelas berhasil diperbarui.'
            ];
            header("Location: list_kelas.php");
            exit;
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Gagal memperbarui data kelas.'
            ];
        }
    }
}

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

<?php include '../../template/topbar.php'; ?>

    <!-- Main Content -->
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Alert -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['alert']['type']); ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['alert']['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <h3 class="mb-3">Edit Kelas</h3>

            <div class="card shadow">
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" value="<?= htmlspecialchars($kelas['nama_kelas']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="list_kelas.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- End of Main Content -->

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End of Content Wrapper -->

<script>
setTimeout(function(){
    $('.alert').fadeOut('slow', function(){ $(this).remove(); });
}, 3000);
</script>
