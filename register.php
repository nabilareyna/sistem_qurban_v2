<?php
include "config/koneksi.php";

if (isset($_POST['register'])) {
    $nik      = $_POST['nik'];
    $name     = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $alamat   = $_POST['alamat'];
    $no_hp    = $_POST['no_hp'];

    // Cek duplikat NIK
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "NIK sudah terdaftar!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (nik, name, username, password, role, alamat, no_hp) VALUES ('$nik', '$name', '$username', '$password', '$role', '$alamat', '$no_hp')");
        if ($insert) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Gagal mendaftar. " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Manajemen Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card login-card shadow-custom">
                        <div class="card-header text-center">
                            <h3><i class="fas fa-user-plus me-2"></i>Registrasi Pengguna</h3>
                        </div>
                        <div class="card-body p-4">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($success)): ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?= $success ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nik" class="form-label">
                                            <i class="fas fa-id-card me-2"></i>NIK
                                        </label>
                                        <input type="text" class="form-control" id="nik" name="nik" 
                                               placeholder="Nomor Induk Kependudukan" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Nama Lengkap
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               placeholder="Nama lengkap" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-at me-2"></i>Username
                                        </label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               placeholder="Username untuk login" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Password
                                        </label>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Password" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="no_hp" class="form-label">
                                            <i class="fas fa-phone me-2"></i>No. HP
                                        </label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                               placeholder="Nomor handphone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-user-tag me-2"></i>Role
                                        </label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="">-- Pilih Role --</option>
                                            <option value="admin">Admin</option>
                                            <option value="panitia">Panitia</option>
                                            <option value="berqurban">Berqurban</option>
                                            <option value="warga">Warga</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="alamat" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                    </label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                              placeholder="Alamat lengkap"></textarea>
                                </div>

                                <button type="submit" name="register" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-user-plus me-2"></i>Daftar
                                </button>
                            </form>

                            <div class="text-center">
                                <p class="mb-0 text-secondary">Sudah punya akun?</p>
                                <a href="index.php" class="text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login Sekarang
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
