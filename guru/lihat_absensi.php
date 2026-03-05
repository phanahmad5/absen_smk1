<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../index.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$kelas         = $_GET['kelas'] ?? '';
$mapel         = $_GET['mapel'] ?? '';

$list_kelas = $conn->query("SELECT DISTINCT kelas FROM absensi ORDER BY kelas ASC");
$list_mapel = $conn->query("SELECT DISTINCT mata_pelajaran FROM absensi ORDER BY mata_pelajaran ASC");

$sql = "
SELECT a.id,a.nisn,s.nama AS nama_siswa,a.kelas,a.mata_pelajaran,
       a.tanggal,a.jam,a.status
FROM absensi a
JOIN siswa s ON a.nisn = s.nisn
JOIN jadwal j ON a.mapel_id = j.mapel
WHERE j.id_guru = ?
";

$params = [$id_guru];
$types = "i";

if ($tanggal_awal) {
    $sql .= " AND a.tanggal = ?";
    $params[] = $tanggal_awal;
    $types .= "s";
}
if ($kelas) {
    $sql .= " AND a.kelas = ?";
    $params[] = $kelas;
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lihat Absensi</title>

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
                    <h1 class="h3 mb-4 text-gray-800">Lihat Absensi</h1>

                    <div class="card shadow mb-4">
                        <div class="card-body table-responsive">

                            <table class="table table-bordered" id="dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $no = 1;
                                    while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nisn'] ?></td>
                                            <td><?= $row['nama_siswa'] ?></td>
                                            <td><?= $row['kelas'] ?></td>
                                            <td><?= $row['mata_pelajaran'] ?></td>
                                            <td><?= $row['tanggal'] ?></td>
                                            <td><?= $row['jam'] ?></td>

                                          <td>
    <?php
    $status = $row['status'] ?? 'Alpha'; // default jika kosong

    $badgeMap = [
        'Hadir' => 'success',
        'Sakit' => 'warning',
        'Izin'  => 'info',
        'Alpha' => 'danger'
    ];

    $badge = $badgeMap[$status] ?? 'secondary';
    ?>
    <span class="badge badge-<?= $badge ?>">
        <?= $status ?>
    </span>
</td>

                                            <td>
                                                <select id="status_<?= $row['id'] ?>" class="form-control form-control-sm">
                                                    <option value="Hadir" <?= $row['status'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                                    <option value="Sakit" <?= $row['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                                    <option value="Izin" <?= $row['status'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                                    <option value="Alpha" <?= $row['status'] == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
                                                </select>

                                                <button type="button"
                                                    class="btn btn-sm btn-primary mt-1 btn-update"
                                                    data-id="<?= $row['id'] ?>">
                                                    Update
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>

                                </tbody>
                            </table>

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
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="fas fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf"></i> PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-print"></i> Print'
                    }
                ]
            });
        });
        $(document).on('click', '.btn-update', function() {
            let id = $(this).data('id');
            let status = $('#status_' + id).val();

            $.post('update_status.php', {
                id: id,
                status: status
            }, function(res) {
                if (res === 'success') {
                    alert('Status berhasil diperbarui');
                    location.reload();
                } else {
                    alert('Gagal update');
                }
            });
        });
    </script>

</body>

</html>