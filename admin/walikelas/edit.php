<?php
session_start();
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';

// Ambil data wali berdasarkan ID
$id = $_GET['id'];
$q = $conn->query("SELECT * FROM wali_kelas WHERE id='$id'");
$d = $q->fetch_assoc();

if (isset($_POST['update'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $kelas    = $_POST['kelas'];
    $password = $_POST['password'];

    // Jika password diisi → update password juga
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->query("UPDATE wali_kelas 
                                SET nama='$nama', username='$username', kelas='$kelas', password='$password_hash' 
                                WHERE id='$id'");
    } else {
        $update = $conn->query("UPDATE wali_kelas 
                                SET nama='$nama', username='$username', kelas='$kelas' 
                                WHERE id='$id'");
    }

    if ($update) {
        echo "<script>alert('Data berhasil diperbarui');window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <h3>Edit Wali Kelas</h3>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?= $d['nama'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= $d['username'] ?>" required>
                </div>
                <div class="mb-3">
    <label>Kelas</label>
    <select name="kelas" class="form-control" required>
        <option value="">-- Pilih Kelas --</option>
        <?php
        $qKelas = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
        while ($k = $qKelas->fetch_assoc()) {
            $selected = ($d['kelas'] == $k['nama_kelas']) ? 'selected' : '';
            echo "<option value='{$k['nama_kelas']}' $selected>{$k['nama_kelas']}</option>";
        }
        ?>
    </select>
</div>

                <div class="mb-3">
                    <label>Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ganti password">
                </div>
                <button type="submit" name="update" class="btn btn-success">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>
