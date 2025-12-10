<?php
session_start();
include '../config/koneksi.php';

// Pastikan login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../index.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

$tanggal_awal  = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$kelas         = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$mapel         = isset($_GET['mapel']) ? $_GET['mapel'] : '';

// Ambil daftar kelas unik dari tabel absensi
$list_kelas = $conn->query("SELECT DISTINCT kelas FROM absensi ORDER BY kelas ASC");

// Ambil daftar mapel unik dari tabel absensi (atau dari tabel jadwal)
$list_mapel = $conn->query("SELECT DISTINCT mata_pelajaran FROM absensi ORDER BY mata_pelajaran ASC");

// Query dasar
$sql = "
    SELECT DISTINCT
        a.id,
        a.nisn,
        s.nama AS nama_siswa,
        a.kelas,
        a.mata_pelajaran,
        a.tanggal,
        a.jam,
        a.status
    FROM absensi a
    JOIN siswa s ON a.nisn = s.nisn
    JOIN jadwal j ON a.mapel_id = j.mapel
    WHERE j.id_guru = ?
";

// Filter dinamis
$params = [$id_guru];
$types  = "i";

if ($tanggal_awal && $tanggal_akhir) {
    $sql .= " AND a.tanggal BETWEEN ? AND ?";
    $params[] = $tanggal_awal;
    $params[] = $tanggal_akhir;
    $types   .= "ss";
}
if ($kelas) {
    $sql .= " AND a.kelas = ?";
    $params[] = $kelas;
    $types   .= "s";
}
if ($mapel) {
    $sql .= " AND a.mata_pelajaran = ?";
    $params[] = $mapel;
    $types   .= "s";
}

$sql .= " ORDER BY a.tanggal DESC, a.jam DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <title>Rekap Absensi - Guru</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.7/css/buttons.bootstrap4.min.css" rel="stylesheet"/>
    <script src="../vendor/jquery/jquery.min.js"></script>
</head>
<body id="page-top">

<div id="wrapper">
    <?php include '../template/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../template/topbar.php'; ?>
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Rekap Absensi</h1>

              
                <!-- Data Absensi -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">Data Kehadiran Siswa</h6>
                    </div>
                      <!-- Filter Form -->
               
                    <div class="card-body">
                        <form method="GET" class="form-inline">
                            <label class="mr-2">Tanggal:</label>
                            <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>" class="form-control mr-2">

                            <label class="mr-2">Kelas:</label>
                            <select name="kelas" class="form-control mr-2">
                                <option value="">-- Semua Kelas --</option>
                                <?php while($k = $list_kelas->fetch_assoc()): ?>
                                    <option value="<?= $k['kelas']; ?>" <?= ($kelas == $k['kelas']) ? 'selected' : ''; ?>>
                                        <?= $k['kelas']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                            <label class="mr-2">Mapel:</label>
                            <select name="mapel" class="form-control mr-2">
                                <option value="">-- Semua Mapel --</option>
                                <?php while($m = $list_mapel->fetch_assoc()): ?>
                                    <option value="<?= $m['mata_pelajaran']; ?>" <?= ($mapel == $m['mata_pelajaran']) ? 'selected' : ''; ?>>
                                        <?= $m['mata_pelajaran']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </form>
                    </div>
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
                                            if ($row['status'] === 'Hadir') {
                                                echo '<span class="badge badge-success">Hadir</span>';
                                            } elseif ($row['status'] === 'Sakit') {
                                                echo '<span class="badge badge-warning">Sakit</span>';
                                            } elseif ($row['status'] === 'Izin') {
                                                echo '<span class="badge badge-info">Izin</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Alpha</span>';
                                            }
                                            ?>
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

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-right"B>>frtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: '<i class="fas fa-file-pdf"></i> PDF' },
            { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class="fas fa-print"></i> Print' }
        ]
    });
});

</script>

</body>
</html>
