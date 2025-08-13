<?php
if (session_status() == PHP_SESSION_NONE) session_start();

include '../../config/koneksi.php';

// Validasi ID
if (!isset($_GET['id'])) {
    header("Location: jadwal.php");
    exit;
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM jadwal WHERE id='$id'");
$j = mysqli_fetch_assoc($data);

// Ambil daftar guru & kelas
$guru = mysqli_query($conn, "SELECT * FROM guru");
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");

// Proses update
if (isset($_POST['update'])) {
    $id_guru = $_POST['id_guru'];
    $kelas = $_POST['kelas'];
    $mapel = $_POST['mapel'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    $query = "UPDATE jadwal SET 
                id_guru='$id_guru',
                kelas='$kelas',
                mapel='$mapel',
                hari='$hari',
                jam_mulai='$jam_mulai',
                jam_selesai='$jam_selesai'
              WHERE id='$id'";
    mysqli_query($conn, $query);

    echo "<script>alert('Data jadwal berhasil diperbarui');window.location='jadwal.php'</script>";
    exit;
}

// Include header dan sidebar dari template
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Page Heading -->
            <h4 class="mb-4">Edit Jadwal</h4>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Form Edit Jadwal</h6>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Guru</label>
                            <select name="id_guru" id="id_guru" class="form-control" required>
                                <?php while($g = mysqli_fetch_array($guru)){ ?>
                                    <option value="<?= $g['id'] ?>" <?= $g['id']==$j['id_guru']?'selected':'' ?>>
                                        <?= $g['nama'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" id="kelas" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                <?php while($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['nama_kelas'] ?>" <?= $k['nama_kelas'] == $j['kelas'] ? 'selected' : '' ?>>
                                        <?= $k['nama_kelas'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mapel" class="form-label">Mata Pelajaran</label>
                            <input type="text" name="mapel" id="mapel" class="form-control" value="<?= $j['mapel'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select name="hari" id="hari" class="form-control" required>
                                <?php 
                                $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                                foreach($hariList as $h){ ?>
                                    <option value="<?= $h ?>" <?= $h==$j['hari']?'selected':'' ?>><?= $h ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="<?= $j['jam_mulai'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="<?= $j['jam_selesai'] ?>" required>
                        </div>

                        <button type="submit" name="update" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="jadwal.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End Content Wrapper -->
