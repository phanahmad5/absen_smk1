<?php 
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

// Include koneksi & template
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

            <!-- Alert Session -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type']; ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $_SESSION['alert']['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h3 class="fw-bold text-primary m-0">
                    <i class=""></i> Daftar Data Siswa
                </h3>
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
                <label for="kelas" class="fw-bold">Filter Kelas:</label>
                <select name="kelas" id="kelas" class="form-select w-auto">
                    <option value="">Semua Kelas</option>
                    <?php
                    // Ambil daftar kelas unik dari tabel siswa
                    $kelasQuery = $conn->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
                    while ($k = $kelasQuery->fetch_assoc()):
                        $selected = (isset($_GET['kelas']) && $_GET['kelas'] == $k['kelas']) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($k['kelas']); ?>" <?= $selected; ?>>
                            <?= htmlspecialchars($k['kelas']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Tampilkan
                </button>
                <?php if (isset($_GET['kelas']) && $_GET['kelas'] != ''): ?>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                <?php endif; ?>
            </form>

            <!-- Card Tabel -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped align-middle text-center" id="tabelSiswa" width="100%">
                            <thead class="table-light">
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>TTL</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th>No Telp</th>
                                    <th>QR Code</th>
                                    <th width="120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Filter berdasarkan kelas jika ada
                                $where = '';
                                if (!empty($_GET['kelas'])) {
                                    $kelas = $conn->real_escape_string($_GET['kelas']);
                                    $where = "WHERE kelas = '$kelas'";
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
                                            <td><img src="<?= htmlspecialchars($d['qr_code']); ?>" width="70" alt="QR"></td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="edit_siswa.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="hapus_siswa.php?id=<?= $d['id']; ?>" 
                                                       onclick="return confirm('Yakin ingin menghapus siswa ini?')" 
                                                       class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php 
                                    endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Tidak ada data siswa untuk kelas ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div> <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End of Content Wrapper -->

<!-- DataTables Script -->
<script>
$(document).ready(function(){
    $('#tabelSiswa').DataTable({
        "pageLength": 10,
        "lengthMenu": [ [10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500] ],
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data",
            "search": "Cari:",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(disaring dari _MAX_ total data)"
        }
    });
});
</script>
