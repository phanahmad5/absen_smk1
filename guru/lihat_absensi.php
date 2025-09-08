<?php
session_start();
include '../config/koneksi.php';

// Pastikan login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../index.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

// Ambil data absensi milik guru ini
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
    ORDER BY a.tanggal DESC, a.jam DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_guru);
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

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">Data Kehadiran Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div id="alert-message"></div>
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
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()) : ?>
                                    <tr id="row-<?= $row['id']; ?>">
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nisn']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                        <td><?= htmlspecialchars($row['kelas']); ?></td>
                                        <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                                        <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                        <td><?= htmlspecialchars($row['jam']); ?></td>
                                        <td class="status-text">
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
                                        <td>
                                            <select class="form-control form-control-sm status-select" data-id="<?= $row['id']; ?>">
                                                <option value="Hadir" <?= $row['status'] == 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
                                                <option value="Alpha" <?= $row['status'] == 'Alpha' ? 'selected' : ''; ?>>Alpha</option>
                                                <option value="Sakit" <?= $row['status'] == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
                                                <option value="Izin" <?= $row['status'] == 'Izin' ? 'selected' : ''; ?>>Izin</option>
                                            </select>
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

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();

    $('.status-select').change(function() {
        var id = $(this).data('id');
        var status = $(this).val();
        var row = $('#row-' + id);

        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { id: id, status: status },
            success: function(res) {
                console.log(res); 
                try {
                    var data = (typeof res === "string") ? JSON.parse(res) : res;
                    if (data.success) {
                        var badgeClass = (status === 'Hadir') ? 'badge-success' 
                                        : (status === 'Sakit') ? 'badge-warning' 
                                        : (status === 'Izin') ? 'badge-info' 
                                        : 'badge-danger';
                        row.find('.status-text').html('<span class="badge '+badgeClass+'">'+status+'</span>');
                        $('#alert-message').html('<div class="alert alert-success">'+data.message+'</div>');
                    } else {
                        $('#alert-message').html('<div class="alert alert-danger">'+data.message+'</div>');
                    }
                } catch (e) {
                    $('#alert-message').html('<div class="alert alert-danger">Terjadi kesalahan server.</div>');
                }
            },
            error: function() {
                $('#alert-message').html('<div class="alert alert-danger">Gagal terhubung ke server.</div>');
            }
        });
    });
});
</script>

</body>
</html>
