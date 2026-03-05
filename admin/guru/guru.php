<?php
session_start();
include '../../config/koneksi.php';
include '../../template/header.php';

// Query data guru + status wali kelas
$q = $conn->query("
    SELECT g.id, g.nama, g.nip, g.username, w.kelas
    FROM guru g
    LEFT JOIN wali_kelas w ON g.id = w.guru_id
    ORDER BY g.nama ASC
");
?>

<style>
html, body {
    height: 100%;
    margin: 0;
    overflow: hidden;
}

/* WRAPPER UTAMA */
#wrapper {
    display: flex;
    height: 100vh;
    width: 100vw;
    overflow: hidden;
}

/* CONTENT AREA */
#content {
    flex: 1;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    background-color: #f8f9fc;
}

/* HILANGKAN PADDING DEFAULT */
.container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* ISI KONTEN */
.content-inner {
    padding: 20px;
}
</style>

<div id="wrapper">

    <!-- SIDEBAR -->
    <?php include '../../template/sidebar.php'; ?>

    <!-- CONTENT -->
    <div id="content">
        <?php include '../../template/topbar.php'; ?>

        <div class="container-fluid">
            <div class="content-inner">

                <!-- HEADER HALAMAN -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Data Guru</h3>
                    <a href="tambah_guru.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Guru
                    </a>
                </div>

                <!-- CARD -->
                <div class="card shadow-sm">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="tabelGuru">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Guru</th>
                                        <th>NIP</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; while ($d = $q->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($d['nama']) ?></td>
                                        <td><?= htmlspecialchars($d['nip']) ?></td>
                                        <td><?= htmlspecialchars($d['username']) ?></td>
                                        <td class="text-center">
                                            <?php if ($d['kelas']): ?>
                                                <span class="badge bg-success">
                                                    Wali Kelas <?= htmlspecialchars($d['kelas']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    Bukan Wali Kelas
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit_guru.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="hapus_guru.php?id=<?= $d['id'] ?>"
                                               onclick="return confirm('Yakin hapus guru ini?')"
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

            </div>
        </div>

    </div>
</div>

<?php include '../../template/footer.php'; ?>

<script>
$(document).ready(function () {
    $('#tabelGuru').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                next: "Next",
                previous: "Prev"
            }
        }
    });
});
</script>
