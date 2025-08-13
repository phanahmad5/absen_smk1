<?php
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();

require '../../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include '../../template/header.php';
include '../../template/sidebar.php';

// Ambil data kelas
$query = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

<?php include '../../template/topbar.php'; ?>

    <!-- Main Content -->
    <div id="content">
        <div class="container-fluid mt-4">

            <!-- Alert Session (jika ada) -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['alert']['type']); ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['alert']['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Data Kelas</h3>
                <a href="tambah_kelas.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>

            <!-- Tabel -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="dataKelas" width="100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="50px">No</th>
                                    <th>Nama Kelas</th>
                                    <th width="150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if ($query && mysqli_num_rows($query) > 0) {
                                    while ($d = mysqli_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($d['nama_kelas']); ?></td>
                                    <td class="text-center">
                                        <div class="d-grid gap-2" style="max-width:120px;margin:0 auto;">
                                            <!-- Jika mau tambahkan tombol Edit, bisa ditaruh di sini -->
                                            <a href="edit_kelas.php?id=<?= intval($d['id']); ?>"
   class="btn btn-sm btn-warning">
    <i class="fas fa-edit"></i> Edit
</a>

                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    } // end while
                                } else {
                                    // Tidak ada data
                                ?>
                                <tr>
                                    <td class="text-center" colspan="3">Belum ada data kelas.</td>
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

<script>
$(document).ready(function() {
    // DataTables
    $('#dataKelas').DataTable({
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

    // Auto hide alert setelah 3 detik (jika ada)
    setTimeout(function(){
        $('.alert').fadeOut('slow', function(){ $(this).remove(); });
    }, 3000);
});
</script>
