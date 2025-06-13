<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Query total
$total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='masuk'"))['total'] ?? 0;
$total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='keluar'"))['total'] ?? 0;
//nambah data keuangan
if (isset($_POST['tambah'])) {
    $tipe = $_POST['tipe'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $catatan = $_POST['catatan'];

    $query = mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        '$tipe', '$kategori', '$jumlah', '$catatan', NOW())");

    if ($query) {
        $success = "Transaksi berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan transaksi.";
    }
}
?>

<h2>Rekap Keuangan</h2>
<a href="qurban_list.php">← Data Qurban</a> | <a href="../../logout.php">Logout</a><br><br>

<h3>Input Transaksi Keuangan</h3>
<?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    Tipe:
    <select name="tipe" required>
        <option value="masuk">Masuk</option>
        <option value="keluar">Keluar</option>
    </select><br><br>

    Kategori: <input type="text" name="kategori" required><br><br>
    Jumlah: <input type="number" name="jumlah" required><br><br>
    Catatan: <textarea name="catatan" required></textarea><br><br>
    <button type="submit" name="tambah">Simpan</button>
</form>
<hr>

<table border="1" cellpadding="10">
    <tr>
        <th>Pemasukan</th>
        <td>Rp <?= number_format($total_masuk) ?></td>
    </tr>
    <tr>
        <th>Pengeluaran</th>
        <td>Rp <?= number_format($total_keluar) ?></td>
    </tr>
    <tr>
        <th>Saldo</th>
        <td><b>Rp <?= number_format($total_masuk - $total_keluar) ?></b></td>
    </tr>
</table>

<br>
<h3>Detail Transaksi</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Tanggal</th>
        <th>Tipe</th>
        <th>Kategori</th>
        <th>Jumlah</th>
        <th>Catatan</th>
    </tr>
    <?php
    $data = mysqli_query($conn, "SELECT * FROM keuangan ORDER BY created_at DESC");
    while ($row = mysqli_fetch_assoc($data)) {
        echo "<tr>
            <td>{$row['created_at']}</td>
            <td>{$row['tipe']}</td>
            <td>{$row['kategori']}</td>
            <td>Rp " . number_format($row['jumlah']) . "</td>
            <td>{$row['catatan']}</td>
        </tr>";
    }
    ?>
</table>