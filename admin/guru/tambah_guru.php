<?php
session_start();
include '../../config/koneksi.php';

/* ===============================
   🔒 CEK LOGIN ADMIN
=============================== */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$error = "";

/* ===============================
   🧠 PROSES SIMPAN DATA (SEBELUM OUTPUT)
=============================== */
if (isset($_POST['simpan'])) {
    $nama     = trim($_POST['nama']);
    $nip      = trim($_POST['nip']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 🔍 CEK USERNAME / NIP
    $cek = $conn->prepare("
        SELECT id FROM guru 
        WHERE username = ? OR nip = ?
    ");
    $cek->bind_param("ss", $username, $nip);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "❌ Username atau NIP sudah digunakan!";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO guru (nama, nip, username, password) 
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param("ssss", $nama, $nip, $username, $password);

        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Guru berhasil ditambahkan'
            ];
            header("Location: guru.php");
            exit;
        } else {
            $error = "❌ Gagal menambahkan guru!";
        }
        $stmt->close();
    }
    $cek->close();
}

/* ===============================
   ⬇️ BARU OUTPUT HTML
=============================== */
include '../../template/header.php';
include '../../template/sidebar.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tambah Guru</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="form-group mb-3">
                            <label>Nama Guru</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>

                        <a href="guru.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>
