<?php
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file_mimes = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = IOFactory::createReader('Csv');
        } elseif ('xls' == $extension) {
            $reader = IOFactory::createReader('Xls');
        } else {
            $reader = IOFactory::createReader('Xlsx');
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $berhasil = 0;
        $gagal = 0;

        for ($i = 1; $i < count($sheetData); $i++) {
            $nama = mysqli_real_escape_string($conn, $sheetData[$i][0]);
            $nip = mysqli_real_escape_string($conn, $sheetData[$i][1]);
            $username = mysqli_real_escape_string($conn, $sheetData[$i][2]);
            $password = md5(mysqli_real_escape_string($conn, $sheetData[$i][3]));

            if ($nama != "" && $username != "" && $sheetData[$i][3] != "") {
                $q = mysqli_query($conn, "INSERT INTO guru (nama, nip, username, password) VALUES ('$nama','$nip','$username','$password')");
                if ($q) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } else {
                $gagal++;
            }
        }

        echo "<script>alert('Import selesai! Berhasil: $berhasil | Gagal: $gagal');window.location='guru.php'</script>";
    } else {
        echo "<script>alert('Format file tidak valid!');window.location='import_guru.php'</script>";
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Import Guru dari Excel</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Pilih File Excel</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">Format: .xls, .xlsx (Kolom: Nama | NIP | Username | Password)</small>
                        </div>
                        <button type="submit" name="import" class="btn btn-success">Import</button>
                        <a href="guru.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include '../../template/footer.php'; ?>
