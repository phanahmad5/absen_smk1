<?php
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

// Koneksi database
include '../../config/koneksi.php';

// Include header (sudah ada <html> dan mulai wrapper)
include '../../template/header.php';

// Include sidebar (di dalam wrapper)
include '../../template/sidebar.php';

// Ambil data guru
$query = mysqli_query($conn, "SELECT * FROM guru ORDER BY nama ASC");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <div class="container-fluid mt-4">

            <!-- Header + tombol tambah -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Data Guru</h3>
                <a href="tambah_guru.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Guru
                </a>
            </div>

            <!-- Card Data -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataGuru" class="table table-bordered table-hover align-middle" width="100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="50px">No</th>
                                    <th>Nama Guru</th>
                                    <th>NIP</th>
                                    <th>Username</th>
                                    <th width="130px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no=1; 
                                while($g = mysqli_fetch_array($query)){ ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $g['nama'] ?></td>
                                    <td><?= $g['nip'] ?></td>
                                    <td><?= $g['username'] ?></td>
                                    <td class="text-center">
                                        <a href="edit_guru.php?id=<?= $g['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
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
    $('#dataGuru').DataTable({
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
