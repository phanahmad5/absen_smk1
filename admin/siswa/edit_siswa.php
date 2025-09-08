<?php
// edit_siswa.php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['alert'] = ['type' => 'danger','message' => 'ID siswa tidak ditemukan.'];
    header('Location: index.php'); exit;
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION['alert'] = ['type' => 'danger','message' => 'Data siswa tidak ditemukan.'];
    header('Location: index.php'); exit;
}
$siswa = $result->fetch_assoc();
$stmt->close();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn       = trim($_POST['nisn'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $jk         = trim($_POST['jk'] ?? '');
    $kelas      = trim($_POST['kelas'] ?? '');
    $wali_kelas = trim($_POST['wali_kelas'] ?? '');
    $no_telp    = trim($_POST['no_telp'] ?? '');
    $ttl        = trim($_POST['ttl'] ?? ''); // <=== tambahan

    if ($nisn === '' || $nama === '' || $kelas === '' || $ttl === '') {
        $errorMessage = 'NISN, Nama, Kelas, dan TTL wajib diisi.';
    } else {
        $stmtUp = $conn->prepare("UPDATE siswa 
            SET nisn=?, nama=?, jk=?, kelas=?, wali_kelas=?, no_telp=?, ttl=? 
            WHERE id=?");
        if ($stmtUp) {
            $stmtUp->bind_param("sssssssi", $nisn, $nama, $jk, $kelas, $wali_kelas, $no_telp, $ttl, $id);
            if ($stmtUp->execute()) {
                $_SESSION['alert'] = ['type' => 'success','message' => 'Data siswa berhasil diperbarui.'];
                $stmtUp->close();
                header('Location: index.php'); exit;
            } else {
                $errorMessage = 'Gagal update: ' . $stmtUp->error;
            }
        } else {
            $errorMessage = 'Query error: ' . $conn->error;
        }
    }
}

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <?php include '../../template/topbar.php'; ?>
    <div id="content">
        <div class="container-fluid mt-4">
            <h3>Edit Data Siswa</h3>

            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <div class="card shadow mt-3">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>NISN</label>
                            <input type="text" name="nisn" class="form-control"
                                value="<?= htmlspecialchars($siswa['nisn']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($siswa['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>TTL (Tempat, Tanggal Lahir)</label>
                            <input type="text" name="ttl" class="form-control"
                                value="<?= htmlspecialchars($siswa['ttl']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jk" class="form-control" required>
                                <option value="Laki-Laki" <?= ($siswa['jk'] === 'Laki-Laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($siswa['jk'] === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Kelas</label>
                            <select name="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php
                                $kelasQuery = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                                while ($k = mysqli_fetch_assoc($kelasQuery)) {
                                    $selected = ($siswa['kelas'] === $k['nama_kelas']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($k['nama_kelas'])."' $selected>" . 
                                            htmlspecialchars($k['nama_kelas']) . 
                                         "</option>";
                                }
                                ?>
                            </select>
                        </div>
                       <div class="mb-3">
    <label>Wali Kelas</label>
    <select name="wali_kelas" class="form-control" required>
        <option value="">-- Pilih Wali Kelas --</option>
        <?php
        $waliQuery = mysqli_query($conn, "SELECT * FROM wali_kelas ORDER BY nama ASC");
        while ($w = mysqli_fetch_assoc($waliQuery)) {
            // Kalau siswa.wali_kelas sama dengan nama wali_kelas, set selected
            $selected = ($siswa['wali_kelas'] == $w['nama']) ? 'selected' : '';
            echo "<option value='".htmlspecialchars($w['nama'])."' $selected>" .
                    htmlspecialchars($w['nama']) . " - " . htmlspecialchars($w['kelas']) .
                 "</option>";
        }
        ?>
    </select>
</div>


                        <div class="mb-3">
                            <label>No Telp</label>
                            <input type="text" name="no_telp" class="form-control"
                                value="<?= htmlspecialchars($siswa['no_telp']); ?>" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php include '../../template/footer.php'; ?>
</div>
