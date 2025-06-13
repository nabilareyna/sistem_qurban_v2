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
<html>

<head>
    <title>Login Sistem Qurban</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Login</h2>
    <?php if (isset($error))
        echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>
</body>

</html>