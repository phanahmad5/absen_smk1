<?php
// Pastikan session aktif
if (session_status() == PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';
include '../../vendor/phpqrcode-master/qrlib.php';

// Proses simpan data
if (isset($_POST['simpan'])) {
    $nisn        = mysqli_real_escape_string($conn, trim($_POST['nisn']));
    $nama        = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $tempat_lahir = mysqli_real_escape_string($conn, trim($_POST['tempat_lahir']));
    $tanggal_lahir = mysqli_real_escape_string($conn, trim($_POST['tanggal_lahir']));
    $jk          = mysqli_real_escape_string($conn, $_POST['jk']);
    $kelas       = mysqli_real_escape_string($conn, $_POST['kelas']);
    $wali_kelas  = mysqli_real_escape_string($conn, trim($_POST['wali_kelas']));
    $telp        = mysqli_real_escape_string($conn, trim($_POST['telp']));

    // Gabungkan tempat dan tanggal lahir
    $ttl = $tempat_lahir . ', ' . date('d F Y', strtotime($tanggal_lahir));

    // Validasi NISN dan No Telp angka
    if (!ctype_digit($nisn)) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'NISN harus berupa angka!'];
        header("Location: tambah.php");
        exit;
    }

    if (!ctype_digit($telp)) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'No Telp harus berupa angka!'];
        header("Location: tambah.php");
        exit;
    }

    // Cek NISN duplikat
    $cek = $conn->query("SELECT nisn FROM siswa WHERE nisn = '$nisn'");
    if ($cek->num_rows > 0) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'NISN sudah terdaftar!'];
        header("Location: tambah.php");
        exit;
    }

    // Buat folder QR per kelas
    $path = "qrcodes/" . $kelas . "/";
    if (!file_exists($path)) mkdir($path, 0777, true);

    // Data QR
    $data_qr = $nisn . "|" . $kelas;
    $tempQr = $path . "temp_" . $nisn . ".png";
    QRcode::png($data_qr, $tempQr, QR_ECLEVEL_L, 6);

    // Tambah teks nama di bawah QR
    $qr = imagecreatefrompng($tempQr);
    $qrWidth = imagesx($qr);
    $qrHeight = imagesy($qr);

    $newHeight = $qrHeight + 50;
    $img = imagecreatetruecolor($qrWidth, $newHeight);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    imagecopy($img, $qr, 0, 0, 0, 0, $qrWidth, $qrHeight);

    $textColor = imagecolorallocate($img, 0, 0, 0);
    $fontFile = __DIR__ . "/../../assets/fonts/arial.ttf";
    $text = $nama;

    if (file_exists($fontFile)) {
        $maxFontSize = 14;
        $minFontSize = 8;
        $fontSize = $maxFontSize;

        do {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $textWidth = $bbox[2] - $bbox[0];
            if ($textWidth > $qrWidth - 10) {
                $fontSize--;
            } else {
                break;
            }
        } while ($fontSize >= $minFontSize);

        $x = ($qrWidth - $textWidth) / 2;
        $y = $qrHeight + 30;
        imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontFile, $text);
    } else {
        $fontSize = 4;
        $fontHeight = imagefontheight($fontSize);
        $fontWidth = imagefontwidth($fontSize);
        $textWidth = $fontWidth * strlen($text);
        imagestring($img, $fontSize, ($qrWidth - $textWidth) / 2, $qrHeight + 10, $text, $textColor);
    }

    $file = $path . $nisn . ".png";
    imagepng($img, $file);
    imagedestroy($img);
    imagedestroy($qr);
    unlink($tempQr);

    // Simpan ke database
    $query = "INSERT INTO siswa (nisn, nama, ttl, jk, kelas, wali_kelas, no_telp, qr_code) 
              VALUES ('$nisn', '$nama', '$ttl', '$jk', '$kelas', '$wali_kelas', '$telp', '$file')";

    if ($conn->query($query)) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Data siswa berhasil disimpan!'];
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Gagal menyimpan data: ' . $conn->error];
        header("Location: tambah.php");
        exit;
    }
}

// Header dan sidebar
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">
            <h3 class="mb-4">Tambah Siswa</h3>

            <div class="card shadow">
                <div class="card-body">
                    <form method="post">
                        <div class="form-group mb-3">
                            <label for="nisn">NISN</label>
                            <input type="text" name="nisn" id="nisn" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control" placeholder="Klik untuk pilih tanggal" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="jk">Jenis Kelamin</label>
                            <select name="jk" id="jk" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki - Laki">Laki - Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kelas">Kelas</label>
                            <select name="kelas" id="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php
                                $kelasQuery = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                                while ($k = mysqli_fetch_assoc($kelasQuery)) {
                                    echo "<option value='" . htmlspecialchars($k['nama_kelas']) . "'>" . htmlspecialchars($k['nama_kelas']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="wali_kelas">Wali Kelas</label>
                            <select name="wali_kelas" id="wali_kelas" class="form-control" required>
                                <option value="">-- Pilih Wali Kelas --</option>
                                <?php
                                $waliQuery = mysqli_query($conn, "SELECT * FROM wali_kelas ORDER BY nama ASC");
                                while ($w = mysqli_fetch_assoc($waliQuery)) {
                                    echo "<option value='" . htmlspecialchars($w['nama']) . "'>" . htmlspecialchars($w['nama']) . " (" . htmlspecialchars($w['kelas']) . ")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="telp">No Telp</label>
                            <input type="text" name="telp" id="telp" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" name="simpan" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../template/footer.php'; ?>
</div>

<!-- Tambahkan jQuery UI Datepicker -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function() {
    $("#tanggal_lahir").datepicker({
        dateFormat: "dd MM yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "1970:2025",
        showAnim: "slideDown"
    });
});
</script>
