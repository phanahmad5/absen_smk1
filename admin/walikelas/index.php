<?php
session_start();
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';

// 🔹 Ambil data wali kelas + data guru
$q = $conn->query("
    SELECT 
        w.id AS wali_id,
        g.nama,
        g.username,
        g.nip,
        w.kelas
    FROM wali_kelas w
    JOIN guru g ON w.guru_id = g.id
    ORDER BY w.kelas ASC
");
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
                        <th>No</th>
                        <th>Nama Guru</th>
                        <th>Username</th>
                        <th>NIP</th>
                        <th>Kelas</th>
                        <th width="120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($d = $q->fetch_assoc()) : 
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><?= htmlspecialchars($d['username']) ?></td>
                        <td><?= htmlspecialchars($d['nip']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= htmlspecialchars($d['kelas']) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $d['wali_id'] ?>" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="hapus.php?id=<?= $d['wali_id'] ?>" 
                               onclick="return confirm('Yakin hapus wali kelas ini?')" 
                               class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>

<script>
$(document).ready(function(){
    $('#tabelWali').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                next: "Next",
                previous: "Prev"
            }
        }
    });
});
</script>
