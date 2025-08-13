<?php 
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

// Include koneksi
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <?php include '../../template/topbar.php'; ?>

    <!-- Main Content -->
    <div id="content">

        <div class="container-fluid mt-4">

            <!-- Alert Session -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['alert']['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Daftar Data Siswa</h3>
                <div>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </a>
                    <a href="download_semua_qr.php" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Semua QR
                    </a>
                    <a href="input_excel.php" class="btn btn-info">
    <i class="fas fa-file-import"></i> Import Excel
</a>

                </div>
            </div>

            <!-- Card Tabel -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="tabelSiswa" width="100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th>No Telp</th>
                                    <th>QR Code</th>
                                    <th width="100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = $conn->query("SELECT * FROM siswa");
                                while($d = $q->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $d['nisn'] ?></td>
                                    <td><?= $d['nama'] ?></td>
                                    <td class="text-center"><?= $d['jk'] ?></td>
                                    <td class="text-center"><?= $d['kelas'] ?></td>
                                    <td class="text-center"><?= $d['wali_kelas'] ?></td>
                                    <td><?= $d['no_telp'] ?></td>
                                    <td class="text-center">
                                        <img src="<?= $d['qr_code'] ?>" width="70" alt="QR">
                                    </td>
                                    <td class="text-center">
                                        <div class="d-grid gap-2">
                                            <a href="edit_siswa.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="hapus_siswa.php?id=<?= $d['id'] ?>" 
                                               onclick="return confirm('Yakin ingin menghapus siswa ini?')" 
                                               class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
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

</div> <!-- End of Page Wrapper -->

<!-- DataTables Script -->
<script>
$(document).ready(function(){
    $('#tabelSiswa').DataTable({
        "pageLength": 10,
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
