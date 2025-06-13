<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";
require_once "../../assets/phpqrcode/qrlib.php";

// Ambil semua data distribusi + user
$data = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d 
    JOIN users u ON d.user_nik = u.nik ORDER BY u.role DESC");

// Lokasi folder simpan QR
$folder = "../../assets/qrcodes/";
if (!file_exists($folder)) mkdir($folder);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print QR Distribusi</title>
    <style>
        .card {
            width: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            float: left;
            text-align: center;
            font-family: sans-serif;
        }
        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h2>QR Code Distribusi Warga</h2>
    <button onclick="window.print()" class="no-print">🖨️ Cetak Semua</button>
    <div class="clearfix">
    <?php while ($row = mysqli_fetch_assoc($data)) {
        $qr_file = $folder . $row['user_nik'] . ".png";
        if (!file_exists($qr_file)) {
            QRcode::png($row['token'], $qr_file, QR_ECLEVEL_H, 6);
        }
    ?>
        <div class="card">
            <h4><?= $row['name'] ?></h4>
            <p>NIK: <?= $row['user_nik'] ?></p>
            <p>Role: <?= $row['role'] ?></p>
            <p>Jumlah Daging: <?= $row['jumlah_daging'] ?> gr</p>
            <img src="<?= $qr_file ?>" width="150"><br>
            <small>Token: <?= $row['token'] ?></small>
        </div>
    <?php } ?>
    </div>
</body>
</html>
