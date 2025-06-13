<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_GET['nik'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'");
$user = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET name='$name', username='$username', password='$hashed', role='$role', alamat='$alamat', no_hp='$no_hp' WHERE nik='$nik'";
    } else {
        $update = "UPDATE users SET name='$name', username='$username', role='$role', alamat='$alamat', no_hp='$no_hp' WHERE nik='$nik'";
    }

    if (mysqli_query($conn, $update)) {
        header("Location: user_list.php");
    } else {
        echo "Update gagal: " . mysqli_error($conn);
    }
}
?>

<h2>Edit User</h2>
<form method="POST">
    Nama: <input type="text" name="name" value="<?= $user['name'] ?>" required><br><br>
    Username: <input type="text" name="username" value="<?= $user['username'] ?>" required><br><br>
    Password: <input type="password" name="password"> (kosongkan jika tidak ganti)<br><br>
    No HP: <input type="text" name="no_hp" value="<?= $user['no_hp'] ?>"><br><br>
    Alamat: <textarea name="alamat"><?= $user['alamat'] ?></textarea><br><br>
    Role:
    <select name="role" required>
        <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
        <option value="panitia" <?= $user['role']=='panitia' ? 'selected' : '' ?>>Panitia</option>
        <option value="berqurban" <?= $user['role']=='berqurban' ? 'selected' : '' ?>>Berqurban</option>
        <option value="warga" <?= $user['role']=='warga' ? 'selected' : '' ?>>Warga</option>
    </select><br><br>
    <button type="submit" name="update">Simpan Perubahan</button>
</form>
<br>
<a href="user_list.php">← Kembali ke daftar user</a>
