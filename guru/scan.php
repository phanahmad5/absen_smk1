<?php
session_start();
require '../config/koneksi.php';

// ===============================
// VALIDASI LOGIN GURU
// ===============================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

date_default_timezone_set('Asia/Jakarta');

// ===============================
// AMBIL PARAMETER
// ===============================
$pertemuan_id = (int)($_GET['pertemuan_id'] ?? 0);
if ($pertemuan_id === 0) {
    exit('Pertemuan tidak valid');
}

// ===============================
// AMBIL DATA PERTEMUAN + JADWAL
// ===============================
$stmt = $conn->prepare("
    SELECT 
        p.id AS pertemuan_id,
        p.tanggal,
        p.status,
        j.kelas,
        j.mapel AS mapel_id,
        m.nama_mapel,
        j.jam_mulai,
        j.jam_selesai
    FROM pertemuan p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN mapel m ON j.mapel = m.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $pertemuan_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    exit('Pertemuan tidak ditemukan');
}

// ===============================
// VALIDASI PERTEMUAN AKTIF
// ===============================
$hariIni = date('Y-m-d');
$jamSekarang = date('H:i:s');

if ($data['tanggal'] !== $hariIni || $data['status'] !== 'dibuka') {
    exit('Pertemuan tidak aktif');
}

$now   = strtotime($jamSekarang);
$start = strtotime($data['jam_mulai']) - (15 * 60);
$end   = strtotime($data['jam_selesai']) + (15 * 60);

$bolehScan = !($now < $start || $now > $end);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Scan QR Absensi</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 30px;
        }
        #reader {
            width: 320px;
            margin: auto;
        }
        #status {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h3>
    Scan QR Code Siswa<br>
    <?= htmlspecialchars($data['nama_mapel']) ?> (<?= htmlspecialchars($data['kelas']) ?>)
</h3>

<p>
    Tanggal: <?= date('d-m-Y') ?> |
    Jam: <?= htmlspecialchars($data['jam_mulai']) ?> - <?= htmlspecialchars($data['jam_selesai']) ?>
</p>

<?php if (!$bolehScan): ?>
    <div style="color:red; font-weight:bold;">
        Absensi di luar jam pertemuan
    </div>
<?php else: ?>

<div id="reader"></div>
<div id="status"></div>
<audio id="beep" src="../assets/sounds/beep.mp3" preload="auto"></audio>

<script>
const pertemuanId = "<?= $pertemuan_id ?>";
const reader = new Html5Qrcode("reader");
const statusEl = document.getElementById('status');
const beep = document.getElementById('beep');

function startScan() {
    reader.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        async (decodedText) => {
            reader.pause();

            let nisn = "";

            // Parse QR
            try {
                const data = JSON.parse(decodedText);
                nisn = data.nisn ?? "";
            } catch {
                if (decodedText.includes("|")) {
                    nisn = decodedText.split("|")[0];
                } else {
                    nisn = decodedText;
                }
            }

            if (!nisn) {
                alert("QR Code tidak valid");
                reader.resume();
                return;
            }

            statusEl.textContent = "Memproses absensi...";

            try {
                const res = await fetch(
                    `simpan_absensi.php?pertemuan_id=${pertemuanId}&nisn=${encodeURIComponent(nisn)}`
                );
                const text = await res.text();

                beep.currentTime = 0;
                beep.play().catch(() => {});

                alert(text);
            } catch (err) {
                alert("Terjadi kesalahan");
            }

            statusEl.textContent = "";
            reader.resume();
        },
        () => {}
    ).catch(err => {
        alert("Gagal mengakses kamera: " + err);
    });
}

startScan();
</script>

<?php endif; ?>

</body>
</html>
