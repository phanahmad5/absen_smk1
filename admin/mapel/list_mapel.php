<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Koneksi database
include '../../config/koneksi.php';

// Ambil semua data mapel
$mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY kode_mapel ASC");

// Template
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <?php include '../../template/topbar.php'; ?>
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Header + tombol tambah -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Daftar Mata Pelajaran</h3>
                <a href="tambah_mapel.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Mapel
                </a>
            </div>

            <!-- Card Data -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataMapel" class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="50px">No</th>
                                    <th>Kode Mapel</th>
                                    <th>Nama Mapel</th>
                                    <th width="150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($m = mysqli_fetch_assoc($mapel)) { ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="text-center"><?= $m['kode_mapel'] ?></td>
                                        <td><?= $m['nama_mapel'] ?></td>
                                        <td class="text-center">
                                            <a href="edit_mapel.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="hapus_mapel.php?id=<?= $m['id'] ?>" 
   onclick="return confirm('Yakin ingin menghapus mapel ini?')"
   class="btn btn-sm btn-danger">
    <i class="fas fa-trash"></i> Hapus
</a>

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Card -->

        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>
<!-- End Content Wrapper -->

</div> <!-- End #wrapper -->

<!-- DataTables Init -->
<script>
$(document).ready(function() {
    $('#dataMapel').DataTable({
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
