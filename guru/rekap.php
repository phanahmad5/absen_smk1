<?php
session_start();
include '../config/koneksi.php';
include '../template/header.php';    // SB Admin 2 header
include '../template/sidebar.php';   // SB Admin 2 sidebar


// Validasi parameter GET
if (!isset($_GET['kelas']) || !isset($_GET['mapel'])) {
    echo "<div class='alert alert-danger m-4'>Parameter kelas dan mapel tidak ditemukan.</div>";
    include '../template/footer.php';
    exit;
}

$kelas = $_GET['kelas'];
$mapel = $_GET['mapel'];
?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Rekap Absensi</h1>

    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="m-0 font-weight-bold">Mata Pelajaran: <?= htmlspecialchars($mapel) ?> | Kelas: <?= htmlspecialchars($kelas) ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabelRekap" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT absensi.*, siswa.nama 
                                                FROM absensi 
                                                JOIN siswa ON absensi.nisn = siswa.nisn 
                                                WHERE absensi.kelas = ? AND absensi.mata_pelajaran = ?
                                                ORDER BY absensi.tanggal DESC");
                        $stmt->bind_param("ss", $kelas, $mapel);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($d = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($d['tanggal']) . "</td>
                                <td>" . htmlspecialchars($d['nisn']) . "</td>
                                <td>" . htmlspecialchars($d['nama']) . "</td>
                                <td>" . htmlspecialchars($d['jam']) . "</td>
                                <td>" . htmlspecialchars($d['status']) . "</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include '../template/footer.php'; ?>

<!-- DataTables JS -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelRekap').DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "ordering": true,
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "zeroRecords": "Tidak ada data ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total entri)",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Berikutnya"
                }
            }
        });
    });
</script>
