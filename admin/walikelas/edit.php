<?php
session_start();
include '../../config/koneksi.php';

// Ambil daftar kelas untuk dropdown
$kelasQuery = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");

// Ambil ID wali kelas dari query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data wali kelas
$stmt = $conn->prepare("SELECT * FROM wali_kelas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$wali = $result->fetch_assoc();

if (!$wali) {
    $_SESSION['alert'] = ['type'=>'danger','message'=>'Data wali kelas tidak ditemukan'];
    header("Location: index.php");
    exit;
}

// Proses update
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // kosong = tidak diubah
    $kelas = trim($_POST['kelas']);

    if (!empty($password)) {
        $passHash = md5($password); // bisa diganti password_hash()
        $q = $conn->query("UPDATE wali_kelas SET nama='$nama', username='$username', password='$passHash', kelas='$kelas' WHERE id=$id");
    } else {
        $q = $conn->query("UPDATE wali_kelas SET nama='$nama', username='$username', kelas='$kelas' WHERE id=$id");
    }

    if ($q) {
        $_SESSION['alert'] = ['type'=>'success','message'=>'Data wali kelas berhasil diperbarui'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal memperbarui data: " . $conn->error;
    }
}

?>

<?php include '../../template/header.php'; ?>
<?php include '../../template/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow col-md-6">
        <div class="card-body">
            <h4>Edit Wali Kelas</h4>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($wali['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($wali['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Password (kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($k = $kelasQuery->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['nama_kelas']) ?>" <?= $wali['kelas'] == $k['nama_kelas'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button name="simpan" class="btn btn-success">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>
