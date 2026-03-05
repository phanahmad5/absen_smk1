<?php
session_start();
require '../config/koneksi.php';

// ================= VALIDASI LOGIN =================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../index.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

// ================= FILTER =================
$tanggal = $_GET['tanggal'] ?? '';
$kelas   = $_GET['kelas'] ?? '';
$mapel   = $_GET['mapel'] ?? '';

// ================= LIST FILTER =================
$list_kelas = $conn->prepare("
    SELECT DISTINCT a.kelas 
    FROM absensi a
    JOIN pertemuan p ON a.pertemuan_id = p.id
    JOIN jadwal j ON p.jadwal_id = j.id
    WHERE j.id_guru = ?
    ORDER BY a.kelas
");
$list_kelas->bind_param("i", $id_guru);
$list_kelas->execute();
$list_kelas = $list_kelas->get_result();

$list_mapel = $conn->prepare("
    SELECT DISTINCT a.mata_pelajaran 
    FROM absensi a
    JOIN pertemuan p ON a.pertemuan_id = p.id
    JOIN jadwal j ON p.jadwal_id = j.id
    WHERE j.id_guru = ?
    ORDER BY a.mata_pelajaran
");
$list_mapel->bind_param("i", $id_guru);
$list_mapel->execute();
$list_mapel = $list_mapel->get_result();

// ================= QUERY UTAMA =================
$sql = "
SELECT 
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
JOIN pertemuan p ON a.pertemuan_id = p.id
JOIN jadwal j ON p.jadwal_id = j.id
WHERE j.id_guru = ?
";

$params = [$id_guru];
$types  = "i";

if ($tanggal) {
    $sql .= " AND a.tanggal = ?";
    $params[] = $tanggal;
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
$data = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

<?php include '../template/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php include '../template/topbar.php'; ?>

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Rekap Absensi Siswa</h1>

<!-- ================= FILTER ================= -->
<form method="GET" class="row mb-3">
    <div class="col-md-3">
        <select name="kelas" class="form-control">
            <option value="">Semua Kelas</option>
            <?php while ($k = $list_kelas->fetch_assoc()): ?>
                <option value="<?= $k['kelas'] ?>" <?= $kelas == $k['kelas'] ? 'selected' : '' ?>>
                    <?= $k['kelas'] ?>
                </option>
            <?php endwhile ?>
        </select>
    </div>

    <div class="col-md-3">
        <select name="mapel" class="form-control">
            <option value="">Semua Mapel</option>
            <?php while ($m = $list_mapel->fetch_assoc()): ?>
                <option value="<?= $m['mata_pelajaran'] ?>" <?= $mapel == $m['mata_pelajaran'] ? 'selected' : '' ?>>
                    <?= $m['mata_pelajaran'] ?>
                </option>
            <?php endwhile ?>
        </select>
    </div>

    <div class="col-md-3">
        <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary btn-block">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>
</form>

<!-- ================= TABEL ================= -->
<div class="card shadow mb-4">
<div class="card-body table-responsive">

<table class="table table-bordered" id="dataTable">
<thead class="thead-dark text-center">
<tr>
    <th>No</th>
    <th>NISN</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>Mapel</th>
    <th>Tanggal</th>
    <th>Jam</th>
    <th>Status</th>
</tr>
</thead>
<tbody class="text-center">

<?php $no=1; while ($r = $data->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $r['nisn'] ?></td>
    <td><?= htmlspecialchars($r['nama_siswa']) ?></td>
    <td><?= $r['kelas'] ?></td>
    <td><?= $r['mata_pelajaran'] ?></td>
    <td><?= $r['tanggal'] ?></td>
    <td><?= $r['jam'] ?></td>
    <td>
        <?php
        $badge = [
            'Hadir'=>'success',
            'Sakit'=>'warning',
            'Izin'=>'info',
            'Alpha'=>'danger'
        ][$r['status']] ?? 'secondary';
        ?>
        <span class="badge badge-<?= $badge ?>">
            <?= $r['status'] ?>
        </span>
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

<!-- ================= JS ================= -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(function () {
    $('#dataTable').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel','pdf','print']
    });

    $('.btn-update').click(function () {
        let id = $(this).data('id');
        let status = $('#status_' + id).val();

        $.post('update_status.php', {id, status}, function (res) {
            if (res === 'success') {
                location.reload();
            } else {
                alert('Gagal update');
            }
        });
    });
});
</script>

</body>
</html>
