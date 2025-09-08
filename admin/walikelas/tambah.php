<?php
session_start();
include '../../config/koneksi.php';

// Ambil daftar kelas untuk dropdown
$kelasQuery = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // bisa diganti password_hash()
    $kelas = trim($_POST['kelas']);

    $q = $conn->query("INSERT INTO wali_kelas (nama, username, password, kelas) 
                       VALUES ('$nama', '$username', '$password', '$kelas')");

    if ($q) {
        $_SESSION['alert'] = ['type'=>'success','message'=>'Wali kelas berhasil ditambahkan'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan wali kelas: " . $conn->error;
    }
}
?>

<?php include '../../template/header.php'; ?>
<?php include '../../template/sidebar.php'; ?>

<div class="container mt-4">
    <div class="card shadow col-md-6">
        <div class="card-body">
            <h4>Tambah Wali Kelas</h4>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password (default = nama)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($k = $kelasQuery->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['nama_kelas']) ?>">
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button name="simpan" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>
