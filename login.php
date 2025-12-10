<?php
session_start();
include 'config/koneksi.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Jika input kosong
    if ($username === '' || $password === '') {
        $err = 'Username / NISN dan Password wajib diisi!';
    } else {
        // ===========================
        // 1. Cek di tabel users (admin)
        // ===========================
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'       => $user['id'],
                    'nama'     => $user['nama_lengkap'],
                    'role'     => $user['role'],
                    'username' => $user['username']
                ];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] === 'siswa') {
                    header("Location: siswa/index.php");
                }
                exit;
            } else {
                $err = 'Password admin salah!';
            }
        } else {
            // ===========================
            // 2. Cek di tabel guru
            // ===========================
            $stmt = $conn->prepare("SELECT * FROM guru WHERE username = ?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($guru = $res->fetch_assoc()) {
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
                // ===========================
                // 3. Cek di tabel wali_kelas
                // ===========================
                $stmt = $conn->prepare("SELECT * FROM wali_kelas WHERE username = ?");
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($wali = $res->fetch_assoc()) {
                    if (md5($password) === $wali['password']) {
                        $_SESSION['user'] = [
                            'id'       => $wali['id'],
                            'nama'     => $wali['nama'],
                            'role'     => 'walikelas',
                            'username' => $wali['username'],
                            'kelas'    => $wali['kelas']
                        ];
                        header("Location: walikelas/index.php");
                        exit;
                    } else {
                        $err = 'Password wali kelas salah!';
                    }
                } else {
                    // ===========================
                    // 4. Cek di tabel siswa
                    // ===========================
                    $stmt = $conn->prepare("SELECT * FROM siswa WHERE nisn = ?");
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($siswa = $res->fetch_assoc()) {
                        // Pastikan password sudah diatur
                        if (!empty($siswa['password'])) {
                            // Verifikasi password hash
                            if (password_verify($password, $siswa['password'])) {
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
                            $err = 'Password siswa belum diatur. Hubungi admin!';
                        }
                    } else {
                        $err = 'Username (NISN) tidak ditemukan!';
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login - Absensi</title>
    <link rel="icon" href="/absensi_smk1kadungora/assets/logosmk1.png" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-login-logo {
            background-color: #fff;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .bg-login-logo img {
            max-width: 250px;
            height: auto;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gradient-info">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- Logo -->
                            <div class="col-lg-6 d-none d-lg-flex bg-login-logo">
                                <img src="/absensi_smk1kadungora/assets/logosmk1.png" alt="Logo Sekolah">
                            </div>

                            <!-- Form Login -->
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang</h1>
                                        <?php if ($err): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="Username / NISN" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-user btn-block">Login</button>
                                        <a href="register.php" class="btn btn-success btn-user btn-block">Register</a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="#">Lupa Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- row -->
                    </div> 
                </div> <!-- card -->
            </div> 
        </div> 
    </div> <!-- container -->

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
