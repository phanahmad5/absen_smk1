<?php
include '../../config/koneksi.php';
include '../../template/header.php';
include '../../template/sidebar.php';


if(isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "INSERT INTO guru (nama, nip, username, password) VALUES ('$nama','$nip','$username','$password')";
    mysqli_query($conn, $query);

    echo "<script>alert('Guru berhasil ditambahkan');window.location='guru.php'</script>";
}
?>


<!-- Begin Page Content -->
<div class="container-fluid">
    
    <h1 class="h3 mb-4 text-gray-800">Tambah Guru</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Guru</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                        <a href="guru.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include '../../template/footer.php'; ?>
