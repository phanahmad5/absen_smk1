<?php
// Session dan koneksi
if (session_status() == PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';
include '../../vendor/phpqrcode-master/qrlib.php';

if (isset($_POST['simpan'])) {
    $nisn        = trim($_POST['nisn']);
    $nama        = trim($_POST['nama']);
    $jk          = $_POST['jk'];
    $kelas       = $_POST['kelas'];
    $wali_kelas  = trim($_POST['wali_kelas']);
    $telp        = trim($_POST['telp']);

    // Cek apakah NISN sudah ada
    $cek = $conn->query("SELECT nisn FROM siswa WHERE nisn = '$nisn'");
    if ($cek->num_rows > 0) {
        echo "<script>alert('NISN sudah terdaftar!'); window.location='tambah.php';</script>";
        exit;
    }

    // Buat folder QR code jika belum ada
    $path = "qrcodes/"; // Pastikan path benar
    if (!file_exists($path)) mkdir($path, 0777, true);

    // Data lengkap yang akan di-encode dalam QR Code
    $data_qr = json_encode([
        'nisn'  => $nisn,
        'kelas' => $kelas
    ]);

    // Generate QR code sementara
    $tempQr = $path . "temp_" . $nisn . ".png";
    QRcode::png($data_qr, $tempQr, QR_ECLEVEL_L, 4);

    // Tambahkan teks (nama) di bawah QR
    $qr = imagecreatefrompng($tempQr);
    $qrWidth = imagesx($qr);
    $qrHeight = imagesy($qr);

    $fontSize = 4;
    $text = $nama;
    $fontHeight = imagefontheight($fontSize);
    $fontWidth = imagefontwidth($fontSize);
    $textWidth = $fontWidth * strlen($text);

    $newHeight = $qrHeight + $fontHeight + 10;
    $img = imagecreatetruecolor($qrWidth, $newHeight);

    // Warna latar belakang putih
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);

    // Gabungkan QR dan teks
    imagecopy($img, $qr, 0, 0, 0, 0, $qrWidth, $qrHeight);
    $textColor = imagecolorallocate($img, 0, 0, 0);
    imagestring($img, $fontSize, ($qrWidth - $textWidth) / 2, $qrHeight + 5, $text, $textColor);

    // Simpan hasil akhir
    $file = $path . $nisn . ".png";
    imagepng($img, $file);

    // Bersihkan resource
    imagedestroy($img);
    imagedestroy($qr);
    unlink($tempQr);

    // Simpan ke database
    $query = "INSERT INTO siswa (nisn, nama, jk, kelas, wali_kelas, no_telp, qr_code) 
              VALUES ('$nisn', '$nama', '$jk', '$kelas', '$wali_kelas', '$telp', '$file')";

    if ($conn->query($query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $conn->error;
    }
}

// Header dan sidebar
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
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
                                while($k = mysqli_fetch_assoc($kelasQuery)) {
                                    echo "<option value='{$k['nama_kelas']}'>{$k['nama_kelas']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="wali_kelas">Wali Kelas</label>
                            <input type="text" name="wali_kelas" id="wali_kelas" class="form-control" required>
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
    <!-- End of Main Content -->
    <?php include '../../template/footer.php'; ?>
</div>
