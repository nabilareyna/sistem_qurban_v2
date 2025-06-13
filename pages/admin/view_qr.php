<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";
require_once "../../assets/phpqrcode/qrlib.php";

if (!isset($_GET['nik'])) {
    echo "NIK tidak ditemukan di URL.";
    exit;
}

$nik = $_GET['nik'];
$check = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d JOIN users u ON d.user_nik = u.nik WHERE d.user_nik='$nik'");
$data = mysqli_fetch_assoc($check);

if (!$data) {
    echo "<p style='color:red;'>Distribusi tidak ditemukan untuk NIK ini.</p>";
    exit;
}

// Buat/generate QR
$folder = "../../assets/qrcodes/";
if (!file_exists($folder))
    mkdir($folder);

$filename = $folder . $data['user_nik'] . ".png";
QRcode::png($data['token'], $filename, QR_ECLEVEL_H, 6);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Kartu QR Distribusi</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }

        .card {
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="card">
        <h3>Kartu Pengambilan Daging</h3>
        <p><strong>Nama:</strong> <?= $data['name'] ?></p>
        <p><strong>NIK:</strong> <?= $data['user_nik'] ?></p>
        <p><strong>Role:</strong> <?= $data['role'] ?></p>
        <p><strong>Jumlah Daging:</strong> <?= $data['jumlah_daging'] ?> gr</p>
        <img src="<?= $filename ?>" width="180"><br>
        <small>Token: <?= $data['token'] ?></small>
    </div>
</body>

</html>