<?php
// ================= SESSION =================
if (session_status() == PHP_SESSION_NONE) session_start();
require '../../config/koneksi.php';

// ================= CEK LOGIN ADMIN =================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

// ================= AMBIL DATA KELAS =================
$query = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<style>
#accordionSidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    height: 100%;
    overflow-y: auto;
}

#content-wrapper {
    margin-left: 224px;
    min-height: 100vh;
}
</style>

<div id="content-wrapper" class="d-flex flex-column">

<?php include '../../template/topbar.php'; ?>

<div id="content">
<div class="container-fluid mt-4">

<!-- ALERT -->
<?php if (!empty($_SESSION['alert'])): ?>
    <div class="alert alert-<?= $_SESSION['alert']['type']; ?> alert-dismissible fade show">
        <?= $_SESSION['alert']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php unset($_SESSION['alert']); endif; ?>

<div class="d-flex justify-content-between mb-3">
    <h3>Data Kelas</h3>
    <a href="tambah_kelas.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kelas
    </a>
</div>

<div class="card shadow">
<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered table-hover" id="dataKelas">
<thead class="table-light text-center">
<tr>
    <th width="50">No</th>
    <th>Nama Kelas</th>
    <th width="120">Aksi</th>
</tr>
</thead>
<tbody>

<?php
$no = 1;
if (mysqli_num_rows($query) > 0):
    while ($row = mysqli_fetch_assoc($query)):
?>
<tr>
    <td class="text-center"><?= $no++; ?></td>
    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
    <td class="text-center">
        <a href="edit_kelas.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr>
    <td colspan="3" class="text-center">Belum ada data</td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>
</div>

</div>
</div>

<?php include '../../template/footer.php'; ?>
</div>

<script>
$(document).ready(function(){
    $('#dataKelas').DataTable({
        language:{
            search:"Cari:",
            lengthMenu:"Tampilkan _MENU_ data",
            info:"Menampilkan _START_ - _END_ dari _TOTAL_ data",
            zeroRecords:"Data tidak ditemukan"
        }
    });

    setTimeout(() => $('.alert').fadeOut(), 3000);
});
</script>
