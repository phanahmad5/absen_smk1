<?php
session_start();
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Data Wali Kelas</h3>
        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Wali Kelas
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="tabelWali">
                <thead class="table-light text-center">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Kelas</th>
                        <th width="120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = $conn->query("SELECT * FROM wali_kelas");
                    while($d = $q->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['username'] ?></td>
                        <td><?= $d['kelas'] ?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="hapus.php?id=<?= $d['id'] ?>" 
                               onclick="return confirm('Yakin hapus wali kelas ini?')" 
                               class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>

<script>
$(document).ready(function(){
    $('#tabelWali').DataTable();
});
</script>
