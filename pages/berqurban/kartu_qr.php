<?php
session_start();
include "../../config/koneksi.php";
require_once "../../assets/phpqrcode/qrlib.php";

$nik = $_SESSION['nik'];

// Cek distribusi
$data = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d 
    JOIN users u ON d.user_nik = u.nik WHERE d.user_nik = '$nik'");

$distribusi = mysqli_fetch_assoc($data);

if (!$distribusi) {
    echo "<p style='color:red;'>Belum ada pembagian daging untuk Anda.</p>";
    exit;
}

// Lokasi simpan QR
$folder = "../../assets/qrcodes/";
if (!file_exists($folder)) mkdir($folder);

$filename = $folder . $distribusi['user_nik'] . ".png";
$isi_qr = $distribusi['token'];

// Generate QR
QRcode::png($isi_qr, $filename, QR_ECLEVEL_H, 6);
?>

<h2>Kartu Pengambilan Daging Qurban</h2>
<p>Nama: <b><?= $distribusi['name'] ?></b></p>
<p>NIK: <b><?= $distribusi['user_nik'] ?></b></p>
<p>Role: <?= $distribusi['role'] ?></p>
<p>Jumlah Daging: <b><?= $distribusi['jumlah_daging'] ?> gr</b></p>
<p>Status: <b><?= strtoupper($distribusi['status_ambil']) ?></b></p>

<img src="<?= $filename ?>" alt="QR Code"><br><br>
<small>Tunjukkan QR ini ke panitia saat pengambilan.</small>
