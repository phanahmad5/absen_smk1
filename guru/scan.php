<?php
session_start();
include '../config/koneksi.php';

// Cek login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo "<script>alert('Akses ditolak'); window.location='../login.php';</script>";
    exit;
}

$id_guru = $_SESSION['user']['id'];
$kelas = $_GET['kelas'] ?? '';
$id_mapel = isset($_GET['id_mapel']) ? (int) $_GET['id_mapel'] : 0;

// Ambil nama mapel dari tabel mapel
$mapel_nama = '';
if ($id_mapel > 0) {
    $stmtMapel = $conn->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
    $stmtMapel->bind_param('i', $id_mapel);
    $stmtMapel->execute();
    $resMapel = $stmtMapel->get_result();
    if ($row = $resMapel->fetch_assoc()) {
        $mapel_nama = $row['nama_mapel'];
    }
    $stmtMapel->close();
}

// Waktu & hari sekarang
date_default_timezone_set('Asia/Jakarta');
$hariConvert = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$hariSekarangEng = date('l');
$hariSekarang = $hariConvert[$hariSekarangEng] ?? $hariSekarangEng;
$jamSekarang = date('H:i:s'); // pakai format detik untuk aman

// Tambahkan toleransi waktu ±15 menit
$toleransiMenit = 15;
$waktuSekarangTimestamp = strtotime($jamSekarang);

// Cek jadwal aktif berdasarkan ID mapel, hari, kelas
$stmtJadwal = $conn->prepare("
    SELECT *, 
           TIME(jam_mulai) AS mulai, 
           TIME(jam_selesai) AS selesai
    FROM jadwal
    WHERE id_guru = ? 
      AND kelas = ? 
      AND mapel = ? 
      AND hari = ?
");
$stmtJadwal->bind_param('isis', $id_guru, $kelas, $id_mapel, $hariSekarang);
$stmtJadwal->execute();
$resJadwal = $stmtJadwal->get_result();

$jadwalAktif = false;
if ($jadwal = $resJadwal->fetch_assoc()) {
    $startTime = strtotime($jadwal['mulai']) - ($toleransiMenit * 60);
    $endTime   = strtotime($jadwal['selesai']) + ($toleransiMenit * 60);

    if ($waktuSekarangTimestamp >= $startTime && $waktuSekarangTimestamp <= $endTime) {
        $jadwalAktif = true;
    }
}
$stmtJadwal->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scan QR Code</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 30px; }
        #reader { width: 320px; margin: auto; }
        #status { margin-top: 10px; font-weight: bold; color: green; }
    </style>
</head>
<body>

<h3>Scan QR Code Siswa - <?= htmlspecialchars($mapel_nama) ?> (<?= htmlspecialchars($kelas) ?>)</h3>

<p>
    <a href="lihat_absensi.php?kelas=<?= urlencode($kelas) ?>&id_mapel=<?= urlencode($id_mapel) ?>" 
       style="display:inline-block; padding:8px 12px; background:#007bff; color:white; text-decoration:none; border-radius:4px;">
        📄 Lihat Data Absensi
    </a>
</p>

<?php if (!$jadwalAktif): ?>
    <div style="color: red; font-weight: bold;">
        Tidak dapat melakukan absensi! <br>
        Jadwal mengajar Anda saat ini tidak aktif. <br>
        Hari: <?= htmlspecialchars($hariSekarang) ?> | Jam: <?= date('H:i') ?>
    </div>
<?php else: ?>
    <div id="reader"></div>
    <div id="status"></div>
    <audio id="beep" src="../assets/sounds/beep.mp3" preload="auto"></audio>

    <script>
    const kelasDefault = "<?= addslashes($kelas) ?>";
    const idMapel = "<?= $id_mapel ?>";
    const mapelNama = "<?= addslashes($mapel_nama) ?>";
    const reader = new Html5Qrcode("reader");
    const beep = document.getElementById('beep');

    function scanQRCode() {
        reader.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            async (decodedText) => {
                reader.pause();

                let nisn = "";
                let kelasQr = kelasDefault;

                try {
                    const data = JSON.parse(decodedText);
                    nisn = data.nisn ?? "";
                    if (data.kelas) kelasQr = data.kelas;
                } catch {
                    nisn = decodedText;
                }

                if (!nisn) {
                    alert("QR Code tidak valid!");
                    reader.resume();
                    return;
                }

                const url = `simpan_absensi.php?nisn=${encodeURIComponent(nisn)}&kelas=${encodeURIComponent(kelasQr)}&id_mapel=${idMapel}&mapel_nama=${encodeURIComponent(mapelNama)}`;
                document.getElementById('status').textContent = "Memproses absensi...";

                try {
                    const res = await fetch(url);
                    const responseText = await res.text();

                    beep.currentTime = 0;
                    beep.play().catch(e => console.warn("Beep gagal:", e));

                    alert(responseText);
                } catch (err) {
                    alert("Terjadi kesalahan: " + err);
                }

                document.getElementById('status').textContent = "";
                reader.resume();
            },
            () => {}
        ).catch((err) => {
            alert("Gagal mengakses kamera: " + err);
        });
    }

    scanQRCode();
    </script>
<?php endif; ?>

</body>
</html>
