<?php
session_start();
include '../../config/koneksi.php';

// hanya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$users = $conn->query("
    SELECT id, username, nama, role FROM (
        SELECT id, username, nama, 'Wali Kelas' AS role FROM wali_kelas
        UNION ALL
        SELECT id, username, nama, 'Guru' AS role FROM guru
        UNION ALL
        SELECT id, nisn AS username, nama, 'Siswa' AS role FROM siswa
    ) AS pengguna
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna</title>

    <!-- SB Admin 2 -->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Custom Scroll Content -->
    <style>
        .content-scroll {
            height: calc(100vh - 70px); /* tinggi layar - topbar */
            overflow-y: auto;
        }
    </style>
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include '../../template/sidebar.php'; ?>
    <!-- End Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content (ONLY THIS SCROLLS) -->
        <div id="content" class="flex-grow-1 content-scroll">

            <!-- Topbar -->
            <?php include '../../template/topbar.php'; ?>
            <!-- End Topbar -->

            <!-- Page Content -->
            <div class="container-fluid mt-4">

                <h1 class="h3 mb-4 text-gray-800">Data Pengguna</h1>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Daftar Pengguna Sistem
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Username / NISN</th>
                                        <th>Nama</th>
                                        <th width="130">Role</th>
                                        <th width="130">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($u = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($u['username']) ?></td>
                                        <td><?= htmlspecialchars($u['nama']) ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit.php?id=<?= $u['id'] ?>&role=<?= $u['role'] ?>"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $u['id'] ?>&role=<?= $u['role'] ?>"
                                               onclick="return confirm('Yakin ingin menghapus user ini?')"
                                               class="btn btn-danger btn-sm">
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
            <!-- End Page Content -->

        </div>
        <!-- End Main Content -->

        <!-- Footer -->
        <?php include '../../template/footer.php'; ?>
        <!-- End Footer -->

    </div>
    <!-- End Content Wrapper -->

</div>
<!-- End Page Wrapper -->

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- JS -->
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../../js/sb-admin-2.min.js"></script>

<!-- DataTables -->
<script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#dataTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            lengthMenu: "Tampilkan _MENU_ entri",
            search: "Cari:",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>

</body>
</html>
