<?php
session_start();
require_once '../config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    echo "<script>alert('Silakan login sebagai siswa!'); window.location='../login_siswa.php';</script>";
    exit;
}

// Ambil data dari sesi
$nisn  = $_SESSION['user']['nisn'] ?? '';
$nama  = $_SESSION['user']['nama'] ?? 'Siswa';
$kelas = $_SESSION['user']['kelas'] ?? '';

if ($nisn === '') {
    echo "<script>alert('NISN tidak ditemukan di sesi!'); window.location='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Saya</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <?php include '../template/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../template/topbar.php'; ?>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Riwayat Absensi - <?= htmlspecialchars($nama) ?></h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Data Absensi Kelas <?= htmlspecialchars($kelas) ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataAbsensi" width="100%" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM absensi WHERE nisn = ? ORDER BY tanggal DESC, jam DESC");
                                    $stmt->bind_param("s", $nisn);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($row['jam']) ?></td>
                                        <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                                        <td>
                                            <span class="badge <?= ($row['status'] === 'Hadir' ? 'badge-success' : 'badge-danger') ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include '../template/footer.php'; ?>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<!-- JS -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- DataTables Export -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataAbsensi').DataTable({
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-right"B>>frtip',
           
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: ">>",
                    previous: "<<"
                }
            }
        });
    });
</script>

</body>
</html>
