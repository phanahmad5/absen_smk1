<?php
session_start();
include 'config/koneksi.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1. Cek di tabel users
    $stmtUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmtUser->bind_param('s', $username);
    $stmtUser->execute();
    $resUser = $stmtUser->get_result();

    if ($user = $resUser->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'nama'     => $user['nama_lengkap'],
                'role'     => $user['role'],
                'username' => $user['username']
            ];
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'siswa') {
                header("Location: siswa/index.php");
            }
            exit;
        } else {
            $err = 'Password salah!';
        }
    } else {
        // 2. Cek di tabel guru
        $stmtGuru = $conn->prepare("SELECT * FROM guru WHERE username = ?");
        $stmtGuru->bind_param('s', $username);
        $stmtGuru->execute();
        $resGuru = $stmtGuru->get_result();

        if ($guru = $resGuru->fetch_assoc()) {
            if (md5($password) === $guru['password']) {
                $_SESSION['user'] = [
                    'id'       => $guru['id'],
                    'nama'     => $guru['nama'],
                    'role'     => 'guru',
                    'username' => $guru['username']
                ];
                header("Location: guru/index.php");
                exit;
            } else {
                $err = 'Password guru salah!';
            }
        } else {
            // 3. Cek di tabel siswa (NISN = username dan password)
            $stmtSiswa = $conn->prepare("SELECT * FROM siswa WHERE nisn = ?");
            $stmtSiswa->bind_param('s', $username);
            $stmtSiswa->execute();
            $resSiswa = $stmtSiswa->get_result();

            if ($siswa = $resSiswa->fetch_assoc()) {
                if ($password === $siswa['nisn']) {
                    $_SESSION['user'] = [
                        'id'       => $siswa['id'],
                        'nama'     => $siswa['nama'],
                        'nisn'     => $siswa['nisn'],
                        'kelas'    => $siswa['kelas'],
                        'role'     => 'siswa',
                        'username' => $siswa['nisn']
                    ];
                    header("Location: siswa/index.php");
                    exit;
                } else {
                    $err = 'Password siswa salah!';
                }
            } else {
                $err = 'Username tidak ditemukan!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - Absensi</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang</h1>
                                        <?php if ($err): ?>
                                            <div class="alert alert-danger"><?= $err ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="Username / NISN" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                        <a href="register.php" class="btn btn-success btn-user btn-block">Register</a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="#">Lupa Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
