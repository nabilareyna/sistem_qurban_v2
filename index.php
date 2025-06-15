<?php
session_start();
include "config/koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['nik'] = $user['nik'];
        $_SESSION['nama'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        //middleware
        if ($user['role'] == 'admin') {
            header("Location: pages/admin/");
        } elseif ($user['role'] == 'panitia') {
            header("Location: pages/panitia/");
        } elseif ($user['role'] == 'berqurban') {
            header("Location: pages/berqurban/");
        } elseif ($user['role'] == 'warga') {
            header("Location: pages/warga/");
        } else {
            echo "Role tidak dikenal!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Qurban - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card login-card shadow-custom">
                        <div class="card-body p-4">
                            <div class="login-header">
                                <i class="fas fa-mosque fa-3x text-gradient mb-3"></i>
                                <h2>Sistem Qurban</h2>
                                <p>Masuk ke akun Anda</p>
                            </div>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user me-2"></i>Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Masukkan username" required>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Masukkan password" required>
                                </div>

                                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                </button>
                            </form>

                            <div class="text-center">
                                <p class="mb-0 text-secondary">Belum punya akun?</p>
                                <a href="register.php" class="text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>