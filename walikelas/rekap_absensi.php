<?php
session_start();
include '../config/koneksi.php';

// Pastikan login wali kelas
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'walikelas') {
    header("Location: ../index.php");
    exit;
}

$id_wali = $_SESSION['user']['id'];

// Ambil nama kelas wali ini
$sql_wali = "SELECT kelas FROM wali_kelas WHERE id = ?";
$stmt_wali = $conn->prepare($sql_wali);
$stmt_wali->bind_param("i", $id_wali);
$stmt_wali->execute();
$result_wali = $stmt_wali->get_result();
$kelas_wali = $result_wali->fetch_assoc()['kelas'] ?? '';

if (!$kelas_wali) {
    die("Anda belum terdaftar sebagai wali kelas.");
}

// Tangkap filter dari form
$tanggal = $_GET['tanggal'] ?? '';
$mapel   = $_GET['mapel'] ?? '';

// Query absensi dengan filter
$sql = "
    SELECT a.id, a.nisn, s.nama AS nama_siswa, a.kelas, a.mata_pelajaran, a.tanggal, a.jam, a.status
    FROM absensi a
    JOIN siswa s ON a.nisn = s.nisn
    WHERE a.kelas = ?
";
$params = [$kelas_wali];
$types = "s";

if ($tanggal) {
    $sql .= " AND a.tanggal = ?";
    $params[] = $tanggal;
    $types .= "s";
}

if ($mapel) {
    $sql .= " AND a.mata_pelajaran = ?";
    $params[] = $mapel;
    $types .= "s";
}

$sql .= " ORDER BY a.tanggal DESC, a.jam DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Ambil daftar mapel unik di kelas ini untuk dropdown
$sql_mapel = "SELECT DISTINCT mata_pelajaran FROM absensi WHERE kelas = ?";
$stmt_mapel = $conn->prepare($sql_mapel);
$stmt_mapel->bind_param("s", $kelas_wali);
$stmt_mapel->execute();
$result_mapel = $stmt_mapel->get_result();
$mapel_list = [];
while($row = $result_mapel->fetch_assoc()){
    $mapel_list[] = $row['mata_pelajaran'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <title>Rekap Absensi Kelas Saya</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="../vendor/jquery/jquery.min.js"></script>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include '../template/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../template/topbar.php'; ?>
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Rekap Absensi Kelas Saya (<?= htmlspecialchars($kelas_wali); ?>)</h1>

                <!-- Filter -->
                <form method="get" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <label for="tanggal" class="mr-2">Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal); ?>">
                    </div>
                    <div class="form-group mr-2">
                        <label for="mapel" class="mr-2">Mata Pelajaran:</label>
                        <select name="mapel" id="mapel" class="form-control">
                            <option value="">Semua</option>
                            <?php foreach($mapel_list as $m): ?>
                                <option value="<?= htmlspecialchars($m); ?>" <?= $mapel === $m ? 'selected' : ''; ?>><?= htmlspecialchars($m); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="rekap_absensi.php" class="btn btn-secondary ml-2">Reset</a>
                </form>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">Data Kehadiran Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nisn']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                        <td><?= htmlspecialchars($row['kelas']); ?></td>
                                        <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                                        <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                        <td><?= htmlspecialchars($row['jam']); ?></td>
                                        <td>
                                            <?php 
                                            $badge = ($row['status'] === 'Hadir') ? 'success' : 
                                                     (($row['status'] === 'Sakit') ? 'warning' : 
                                                     (($row['status'] === 'Izin') ? 'info' : 'danger'));
                                            ?>
                                            <span class="badge badge-<?= $badge ?>"><?= $row['status'] ?></span>
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
<!-- Tambahkan CSS DataTables & Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- Tambahkan jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Tambahkan JS untuk Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        dom: 'Bfrtip', // posisi tombol
        buttons: [
            'excel', 'pdf', 'print'
        ]
    });
});
</script>

</body>
</html>
