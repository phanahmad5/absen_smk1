<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// 🔒 OPTIONAL: Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../config/koneksi.php';

$error = "";

/* ===============================
   PROSES SIMPAN (SEBELUM OUTPUT)
=============================== */
if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode_mapel']);
    $nama = trim($_POST['nama_mapel']);

    // 🔍 CEK DUPLIKAT
    $cek = $conn->prepare("
        SELECT id FROM mapel 
        WHERE kode_mapel = ? OR nama_mapel = ?
    ");
    $cek->bind_param("ss", $kode, $nama);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "❌ Kode atau Nama Mata Pelajaran sudah ada!";
    } else {
        // INSERT DATA
        $stmt = $conn->prepare("
            INSERT INTO mapel (kode_mapel, nama_mapel)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ss", $kode, $nama);

        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Mata pelajaran berhasil ditambahkan'
            ];
            header("Location: list_mapel.php");
            exit;
        } else {
            $error = "❌ Gagal menyimpan data!";
        }
        $stmt->close();
    }
    $cek->close();
}

/* ===============================
   OUTPUT HTML
=============================== */
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <?php include '../../template/topbar.php'; ?>

    <div id="content">
        <div class="container-fluid mt-4">

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">Tambah Mata Pelajaran</h5>
                </div>
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label>Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Nama Mapel</label>
                            <input type="text" name="nama_mapel" class="form-control" required>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="list_mapel.php" class="btn btn-secondary">
                            Batal
                        </a>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>
