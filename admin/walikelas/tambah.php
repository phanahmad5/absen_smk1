<?php
session_start();
include '../../config/koneksi.php';

// 🔹 Ambil guru yang BELUM jadi wali kelas
$guruQuery = $conn->query("
    SELECT g.id, g.nama, g.nip
    FROM guru g
    LEFT JOIN wali_kelas w ON g.id = w.guru_id
    WHERE w.guru_id IS NULL
    ORDER BY g.nama ASC
");

// 🔹 Ambil kelas yang BELUM punya wali
$kelasQuery = $conn->query("
    SELECT k.nama_kelas
    FROM kelas k
    LEFT JOIN wali_kelas w ON k.nama_kelas = w.kelas
    WHERE w.kelas IS NULL
    ORDER BY k.nama_kelas ASC
");

if (isset($_POST['simpan'])) {
    $guru_id = $_POST['guru_id'];
    $kelas = $_POST['kelas'];

    // 🔍 VALIDASI GANDA (AMAN)
    $cek = $conn->prepare("SELECT id FROM wali_kelas WHERE guru_id = ? OR kelas = ?");
    $cek->bind_param("is", $guru_id, $kelas);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "❌ Guru atau kelas sudah memiliki wali!";
    } else {
        $stmt = $conn->prepare("INSERT INTO wali_kelas (guru_id, kelas) VALUES (?, ?)");
        $stmt->bind_param("is", $guru_id, $kelas);

        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Wali kelas berhasil ditambahkan'
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambahkan wali kelas!";
        }
        $stmt->close();
    }
    $cek->close();
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
                    <label>Guru</label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php while ($g = $guruQuery->fetch_assoc()): ?>
                            <option value="<?= $g['id'] ?>">
                                <?= htmlspecialchars($g['nama']) ?> (<?= $g['nip'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = $kelasQuery->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['nama_kelas']) ?>">
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button name="simpan" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>
