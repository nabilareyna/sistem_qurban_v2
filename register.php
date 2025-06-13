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
<html>
<head>
    <title>Registrasi User</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Registrasi User</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        NIK: <input type="text" name="nik" required><br><br>
        Nama Lengkap: <input type="text" name="name" required><br><br>
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        No HP: <input type="text" name="no_hp"><br><br>
        Alamat: <textarea name="alamat"></textarea><br><br>
        Role:
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="panitia">Panitia</option>
            <option value="berqurban">Berqurban</option>
            <option value="warga">Warga</option>
        </select><br><br>
        <button type="submit" name="register">Daftar</button>
    </form>
    <p><a href="index.php">← Kembali ke Login</a></p>
</body>
</html>
