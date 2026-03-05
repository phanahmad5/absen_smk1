<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// OPTIONAL: proteksi admin
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

    $id_guru     = $_POST['id_guru'];
    $kelas       = $_POST['kelas'];
    $id_mapel    = $_POST['id_mapel'];
    $hari        = $_POST['hari'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // 🔍 CEK DUPLIKAT JADWAL
    $cek = $conn->prepare("
        SELECT id FROM jadwal 
        WHERE id_guru = ?
          AND kelas = ?
          AND mapel = ?
          AND hari = ?
          AND jam_mulai = ?
          AND jam_selesai = ?
    ");
    $cek->bind_param(
        "isisss",
        $id_guru,
        $kelas,
        $id_mapel,
        $hari,
        $jam_mulai,
        $jam_selesai
    );
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "❌ Jadwal mengajar ini sudah ada!";
    } else {

        // INSERT DATA
        $stmt = $conn->prepare("
            INSERT INTO jadwal 
            (id_guru, kelas, mapel, hari, jam_mulai, jam_selesai)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isisss",
            $id_guru,
            $kelas,
            $id_mapel,
            $hari,
            $jam_mulai,
            $jam_selesai
        );

        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Jadwal berhasil ditambahkan'
            ];
            header("Location: jadwal.php");
            exit;
        } else {
            $error = "❌ Gagal menyimpan jadwal!";
        }
        $stmt->close();
    }
    $cek->close();
}

/* ===============================
   AMBIL DATA DROPDOWN
=============================== */
$guru  = mysqli_query($conn, "SELECT * FROM guru ORDER BY nama ASC");
$mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");

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
                    <h5 class="m-0">Tambah Jadwal Mengajar</h5>
                </div>
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">

                        <!-- Guru -->
                        <div class="mb-3">
                            <label>Guru</label>
                            <select name="id_guru" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= htmlspecialchars($g['nama']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Kelas -->
                        <div class="mb-3">
                            <label>Kelas</label>
                            <select name="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['nama_kelas'] ?>">
                                        <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Mapel -->
                        <div class="mb-3">
                            <label>Mata Pelajaran</label>
                            <select name="id_mapel" class="form-control" required>
                                <option value="">-- Pilih Mapel --</option>
                                <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= htmlspecialchars($m['nama_mapel']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Hari -->
                        <div class="mb-3">
                            <label>Hari</label>
                            <select name="hari" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                <option>Senin</option>
                                <option>Selasa</option>
                                <option>Rabu</option>
                                <option>Kamis</option>
                                <option>Jumat</option>
                                <option>Sabtu</option>
                            </select>
                        </div>

                        <!-- Jam -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="jadwal.php" class="btn btn-secondary">Kembali</a>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>
