<?php
// edit_siswa.php
// Jangan ada spasi/empty line sebelum tag php

// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

// Sertakan koneksi dulu (ini tidak menghasilkan output)
include '../../config/koneksi.php';

// ----- Proses awal (cek ID & proses POST) SEBELUM include header/sidebar -----

// Ambil ID siswa dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'ID siswa tidak ditemukan.'
    ];
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data siswa berdasarkan ID (prepared statement)
$stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
if (!$stmt) {
    // Jika ada error prepare, set alert dan redirect
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Query error: ' . $conn->error
    ];
    header('Location: index.php');
    exit;
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Data siswa tidak ditemukan.'
    ];
    header('Location: index.php');
    exit;
}

$siswa = $result->fetch_assoc();
$stmt->close();

$errorMessage = ''; // Untuk menampilkan error (jika ada) tanpa redirect

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simple sanitization / trim
    $nisn       = trim($_POST['nisn'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $jk         = trim($_POST['jk'] ?? '');
    $kelas      = trim($_POST['kelas'] ?? '');
    $wali_kelas = trim($_POST['wali_kelas'] ?? '');
    $no_telp    = trim($_POST['no_telp'] ?? '');

    // (Optional) Validasi singkat
    if ($nisn === '' || $nama === '') {
        $errorMessage = 'NISN dan Nama wajib diisi.';
    } else {
        $stmtUp = $conn->prepare("UPDATE siswa 
            SET nisn=?, nama=?, jk=?, kelas=?, wali_kelas=?, no_telp=? 
            WHERE id=?");
        if (!$stmtUp) {
            $errorMessage = 'Gagal menyiapkan query update: ' . $conn->error;
        } else {
            // bind_param: 6 string + 1 int (id)
            $stmtUp->bind_param("ssssssi", $nisn, $nama, $jk, $kelas, $wali_kelas, $no_telp, $id);
            if ($stmtUp->execute()) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Data siswa berhasil diperbarui.'
                ];
                $stmtUp->close();
                header('Location: index.php'); // redirect setelah sukses
                exit;
            } else {
                $errorMessage = 'Gagal memperbarui data siswa: ' . $stmtUp->error;
                $stmtUp->close();
            }
        }
    }
}

// ----- Setelah semua redirect/logic terselesaikan, termasuk template yang menghasilkan output -----
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <?php include '../../template/topbar.php'; ?>

    <!-- Main Content -->
    <div id="content">
        <div class="container-fluid mt-4">
            <h3>Edit Data Siswa</h3>

            <?php
            // Tampilkan alert dari session (jika ada) lalu hapus
            if (isset($_SESSION['alert'])):
                $a = $_SESSION['alert'];
            ?>
                <div class="alert alert-<?= htmlspecialchars($a['type']) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($a['message']) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php
                unset($_SESSION['alert']);
            endif;

            // Jika ada error saat update (tanpa redirect), tampilkan
            if ($errorMessage !== ''): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card shadow mt-3">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>NISN</label>
                            <input type="text" name="nisn" class="form-control" value="<?= htmlspecialchars($siswa['nisn']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jk" class="form-control" required>
                                <option value="L" <?= ($siswa['jk'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?= ($siswa['jk'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="kelas">Kelas</label>
                            <select name="kelas" id="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php
                                $kelasQuery = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                                while($k = mysqli_fetch_assoc($kelasQuery)) {
                                    echo "<option value='{$k['nama_kelas']}'>{$k['nama_kelas']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Wali Kelas</label>
                            <input type="text" name="wali_kelas" class="form-control" value="<?= htmlspecialchars($siswa['wali_kelas']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>No Telp</label>
                            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($siswa['no_telp']); ?>" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div> <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End of Content Wrapper -->
