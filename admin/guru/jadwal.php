<?php
// Mulai session & koneksi
if (session_status() == PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';

// Ambil data jadwal + guru + mapel
$query = mysqli_query($conn, 
    "SELECT jadwal.*, guru.nama AS nama_guru, mapel.nama_mapel 
     FROM jadwal
     INNER JOIN guru ON jadwal.id_guru = guru.id
     INNER JOIN mapel ON jadwal.mapel = mapel.id
     ORDER BY jadwal.hari, jadwal.jam_mulai ASC");
?>

<!-- Custom CSS agar sidebar fix dan content scroll -->
<style>
    /* Sidebar tetap fix di kiri */
    #accordionSidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        height: 100%;
        overflow-y: auto;
        z-index: 1030;
    }

    /* Konten geser ke kanan, hanya konten yang scroll */
    #content-wrapper {
        margin-left: 224px; /* default lebar sidebar SB Admin 2 */
        min-height: 100vh;
        overflow-y: auto;
    }
</style>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include '../../template/topbar.php'; ?>
        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <!-- Judul Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Data Jadwal Mengajar</h3>
                <a href="tambah_jadwal.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            </div>

            <!-- Card Tabel -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataJadwal" class="table table-bordered table-hover align-middle" width="100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th width="180px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while($d = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($d['nama_guru']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($d['kelas']) ?></td>
                                        <td><?= htmlspecialchars($d['nama_mapel']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($d['hari']) ?></td>
                                        <td class="text-center">
                                            <?= date('H:i', strtotime($d['jam_mulai'])) ?> - <?= date('H:i', strtotime($d['jam_selesai'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit_jadwal.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="hapus_jadwal.php?id=<?= $d['id'] ?>" 
                                               onclick="return confirm('Hapus jadwal ini?')" 
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

        </div> <!-- /.container-fluid -->
    </div> <!-- /#content -->

    <?php include '../../template/footer.php'; ?>
</div> <!-- /#content-wrapper -->

</div> <!-- /#wrapper -->

<!-- DataTables Init -->
<script>
$(document).ready(function(){
    $('#dataJadwal').DataTable({
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
