<?php
session_start();
if ($_SESSION['role'] != 'panitia') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Proses validasi token
if (isset($_POST['token'])) {
    $token = $_POST['token'];
    $cek = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d JOIN users u ON d.user_nik = u.nik WHERE d.token='$token'");
    $data = mysqli_fetch_assoc($cek);

    if ($data && $data['status_ambil'] == 'belum') {
        mysqli_query($conn, "UPDATE distribusi SET status_ambil='diambil' WHERE token='$token'");
        $message = "✅ Daging berhasil diberikan ke <strong>{$data['name']}</strong> (NIK: {$data['user_nik']})";
    } elseif ($data && $data['status_ambil'] == 'diambil') {
        $message = "⚠️ Daging untuk NIK {$data['user_nik']} sudah diambil sebelumnya!";
    } else {
        $message = "❌ Token tidak valid atau belum terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Token QR</title>
</head>
<body>
    <h2>🔍 Verifikasi Token Penerima Daging</h2>
    <a href="../../logout.php">Logout</a><br><br>

    <?php if (isset($message)) echo "<p style='font-weight:bold;'>$message</p>"; ?>

    <form method="POST">
        Masukkan Token QR:
        <input type="text" name="token" required style="width:300px;"><br><br>
        <button type="submit">Cek & Tandai Ambil</button>
    </form>
</body>
</html>
