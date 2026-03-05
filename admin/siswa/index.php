<?php 
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content" class="flex-grow-1" style="height:100vh; overflow-y:auto;">
        <?php include '../../template/topbar.php'; ?>

        <div class="container-fluid mt-4">

            <!-- Alert -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type']; ?> alert-dismissible fade show shadow-sm">
                    <?= $_SESSION['alert']['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h3 class="fw-bold text-primary m-0">Daftar Data Siswa</h3>
                <div class="btn-group mt-2 mt-md-0">
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                    <a href="download_semua_qr.php" class="btn btn-success">
                        <i class="fas fa-download"></i> Download QR
                    </a>
                    <a href="input_excel.php" class="btn btn-info">
                        <i class="fas fa-file-import"></i> Import Excel
                    </a>
                </div>
            </div>

            <!-- Filter Kelas -->
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <label class="fw-bold">Filter Kelas:</label>
                <select name="kelas" class="form-select w-auto">
                    <option value="">Semua Kelas</option>
                    <?php
                    $kelasQuery = $conn->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
                    while ($k = $kelasQuery->fetch_assoc()):
                        $selected = (isset($_GET['kelas']) && $_GET['kelas'] == $k['kelas']) ? 'selected' : '';
                    ?>
                        <option value="<?= $k['kelas']; ?>" <?= $selected; ?>>
                            <?= $k['kelas']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button class="btn btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
                <?php if (!empty($_GET['kelas'])): ?>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                <?php endif; ?>
            </form>

            <!-- Tabel -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center align-middle" id="tabelSiswa">
                            <thead class="table-light">
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>TTL</th>
                                    <th>JK</th>
                                    <th>Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th>No Telp</th>
                                    <th>QR</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $where = "";
                                if (!empty($_GET['kelas'])) {
                                    $kelas = $conn->real_escape_string($_GET['kelas']);
                                    $where = "WHERE kelas='$kelas'";
                                }

                                $q = $conn->query("SELECT * FROM siswa $where ORDER BY nama ASC");
                                if ($q->num_rows > 0):
                                    while ($d = $q->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['nisn']); ?></td>
                                            <td class="text-start"><?= htmlspecialchars($d['nama']); ?></td>
                                            <td><?= htmlspecialchars($d['ttl']); ?></td>
                                            <td><?= htmlspecialchars($d['jk']); ?></td>
                                            <td><?= htmlspecialchars($d['kelas']); ?></td>
                                            <td><?= htmlspecialchars($d['wali_kelas']); ?></td>
                                            <td class="text-start"><?= htmlspecialchars($d['no_telp']); ?></td>
                                            <td><img src="<?= $d['qr_code']; ?>" width="70"></td>
                                            
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">

                                                    <!-- Edit -->
                                                    <a href="edit_siswa.php?id=<?= $d['id']; ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Reset Password -->
                                                    <a href="reset_password.php?id=<?= $d['id']; ?>" 
                                                        onclick="return confirm('Reset password siswa ini menjadi 12345?')"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-key"></i>
                                                    </a>

                                                    <!-- Hapus -->
                                                    <a href="hapus_siswa.php?id=<?= $d['id']; ?>" 
                                                       onclick="return confirm('Yakin ingin menghapus siswa ini?')"
                                                       class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="9" class="text-muted">Tidak ada data siswa.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> <!-- /.container-fluid -->
    </div>

    <?php include '../../template/footer.php'; ?>
</div>

<!-- DataTables -->
<script>
$(document).ready(function(){
    $('#tabelSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: "Cari:",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
        }
    });
});
</script>

