<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include '../../config/koneksi.php';
include '../../vendor/autoload.php';
include '../../vendor/phpqrcode-master/qrlib.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file_mimes = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (!empty($_FILES['file_excel']['name']) && in_array($_FILES['file_excel']['type'], $file_mimes)) {

        $arr_file = explode('.', $_FILES['file_excel']['name']);
        $extension = strtolower(end($arr_file));

        if ($extension == 'xls' || $extension == 'xlsx') {
            $reader = IOFactory::createReader(($extension == 'xls') ? 'Xls' : 'Xlsx');
            $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            // Path QR Code
            $path = "qrcodes/";
            if (!file_exists($path)) mkdir($path, 0777, true);

            $successCount = 0;
            $errorCount = 0;

            foreach ($sheetData as $key => $row) {
                // Lewati baris pertama (header)
                if ($key == 0) continue;

                $nisn   = trim($row[0]);
                $nama   = trim($row[1]);
                $jk     = trim($row[2]);
                $kelas  = trim($row[3]);
                $wali_kelas  = trim($row[4]);
                $telp   = trim($row[5]);

                if ($nisn == "" || $nama == "") {
                    $errorCount++;
                    continue;
                }

                // Generate data QR
                $data_qr = json_encode([
                    'nisn'  => $nisn,
                    'kelas' => $kelas
                ]);

                // QR sementara
                $tempQr = $path . "temp_" . $nisn . ".png";
                QRcode::png($data_qr, $tempQr, QR_ECLEVEL_L, 4);

                // Tambahkan teks nama
                $qr = imagecreatefrompng($tempQr);
                $qrWidth = imagesx($qr);
                $qrHeight = imagesy($qr);

                $fontSize = 4;
                $fontHeight = imagefontheight($fontSize);
                $fontWidth = imagefontwidth($fontSize);
                $textWidth = $fontWidth * strlen($nama);

                $newHeight = $qrHeight + $fontHeight + 10;
                $img = imagecreatetruecolor($qrWidth, $newHeight);

                $white = imagecolorallocate($img, 255, 255, 255);
                imagefill($img, 0, 0, $white);

                imagecopy($img, $qr, 0, 0, 0, 0, $qrWidth, $qrHeight);
                $textColor = imagecolorallocate($img, 0, 0, 0);
                imagestring($img, $fontSize, ($qrWidth - $textWidth) / 2, $qrHeight + 5, $nama, $textColor);

                $fileQr = $path . $nisn . ".png";
                imagepng($img, $fileQr);

                imagedestroy($img);
                imagedestroy($qr);
                unlink($tempQr);

                // Simpan ke database
                $sql = "INSERT INTO siswa (nisn, nama, jk, kelas, wali_kelas, no_telp, qr_code)
                        VALUES ('$nisn', '$nama', '$jk', '$kelas', '$wali_kelas', '$telp', '$fileQr')";

                if ($conn->query($sql)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }

            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "Import selesai! Berhasil: $successCount | Gagal: $errorCount"
            ];
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Format file tidak valid!'
        ];
        header("Location: index.php");
        exit;
    }
}

include '../../template/header.php';
include '../../template/sidebar.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid mt-4">
            <h3 class="mb-4">Import Siswa dari Excel</h3>

            <div class="card shadow">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="file_excel">Pilih File Excel (.xls / .xlsx)</label>
                            <input type="file" name="file_excel" id="file_excel" class="form-control" required>
                        </div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" name="import" class="btn btn-success">
                            <i class="fas fa-file-import"></i> Import
                        </button>
                    </form>
                    
                </div>
            </div>

        </div>
    </div>
    <?php include '../../template/footer.php'; ?>
</div>
