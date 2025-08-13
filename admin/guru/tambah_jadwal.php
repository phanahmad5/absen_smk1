<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';

// Proses simpan jadwal
if (isset($_POST['simpan'])) {
    $id_guru     = $_POST['id_guru'];
    $kelas       = $_POST['kelas'];
    $id_mapel    = $_POST['id_mapel']; // ganti mapel → id_mapel
    $hari        = $_POST['hari'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    $query = "INSERT INTO jadwal (id_guru, kelas, mapel, hari, jam_mulai, jam_selesai)
              VALUES ('$id_guru','$kelas','$id_mapel','$hari','$jam_mulai','$jam_selesai')";
    mysqli_query($conn, $query);

    echo "<script>alert('Jadwal berhasil ditambahkan');window.location='jadwal.php'</script>";
}

// Ambil data guru & mapel untuk dropdown
$guru  = mysqli_query($conn, "SELECT * FROM guru ORDER BY nama ASC");
$mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Card Form -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">Tambah Jadwal Mengajar</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <!-- Guru -->
                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Guru</label>
                            <select name="id_guru" id="id_guru" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= $g['id'] ?>"><?= $g['nama'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Kelas -->
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" id="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['nama_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Mapel -->
                        <div class="mb-3">
                            <label for="id_mapel" class="form-label">Mata Pelajaran</label>
                            <select name="id_mapel" id="id_mapel" class="form-control" required>
                                <option value="">-- Pilih Mapel --</option>
                                <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Hari -->
                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select name="hari" id="hari" class="form-control" required>
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
                                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                            </div>
                        </div>

                        <!-- Tombol -->
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
</div> <!-- /#content-wrapper -->
</div> <!-- /#wrapper -->
