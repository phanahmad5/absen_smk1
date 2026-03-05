<?php
session_start();
require '../config/koneksi.php';

// ===============================
// VALIDASI LOGIN GURU
// ===============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$id_guru = $_SESSION['user']['id'];

// ===============================
// AMBIL DATA PERTEMUAN
// ===============================
$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.tanggal,
        p.status,
        j.kelas,
        m.nama_mapel,
        COUNT(a.id) AS total_absen
    FROM pertemuan p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN mapel m ON j.mapel = m.id
    LEFT JOIN absensi a ON a.pertemuan_id = p.id
    WHERE j.id_guru = ?
    GROUP BY p.id
    ORDER BY p.tanggal DESC, p.id DESC
");
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pertemuan</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3 class="mb-3">📅 Data Pertemuan Mengajar</h3>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Mapel</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Jumlah Absen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($row['nama_mapel']) ?></td>
                        <td><?= htmlspecialchars($row['kelas']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'dibuka'): ?>
                                <span class="badge badge-success">Dibuka</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Ditutup</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['total_absen'] ?> siswa</td>
                        <td>
                            <a href="detail_pertemuan.php?id=<?= $row['id'] ?>" 
                               class="btn btn-info btn-sm">
                                🔍 Detail
                            </a>

                            <?php if ($row['status'] === 'dibuka'): ?>
                                <a href="scan.php?pertemuan_id=<?= $row['id'] ?>" 
                                   class="btn btn-success btn-sm">
                                    📷 Scan
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada pertemuan</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
