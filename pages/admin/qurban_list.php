<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

//update status and role
if (isset($_GET['bayar']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data qurban
    $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM qurbans WHERE id='$id'"));
    $user_nik = $q['user_nik'];
    $hewan_id = $q['hewan_id'];
    $jumlah = $q['jumlah'];

    // Ambil harga hewan
    $hewan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hewans WHERE id='$hewan_id'"));
    $harga_per_user = $hewan['jenis'] == 'sapi'
        ? ($hewan['harga'] / 7) * $jumlah
        : $hewan['harga'];

    // Biaya admin (fix: 100.000)
    $biaya_admin = 100000;
    $total = $harga_per_user + $biaya_admin;

    // Update status bayar
    mysqli_query($conn, "UPDATE qurbans SET status_bayar='sudah' WHERE id='$id'");

    // Ubah role user menjadi 'berqurban'
    mysqli_query($conn, "UPDATE users SET role='berqurban' WHERE nik='$user_nik'");

    // Catat pembayaran qurban
    mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        'masuk',
        'pembayaran qurban',
        '$harga_per_user',
        'User NIK $user_nik bayar qurban ($hewan[jenis])',
        NOW())");

    // Catat biaya administrasi
    mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        'masuk',
        'biaya administrasi',
        '$biaya_admin',
        'Biaya admin dari user NIK $user_nik',
        NOW())");

    header("Location: qurban_list.php");
    exit;
}
?>

<h2>Data Qurban Warga</h2>
<a href="hewans.php">← Kelola Hewan</a> | <a href="../../logout.php">Logout</a><br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Jenis Hewan</th>
        <th>Jumlah</th>
        <th>Status Bayar</th>
        <th>Aksi</th>
    </tr>
    <?php
    $no = 1;
    $qurban = mysqli_query($conn, "SELECT q.*, u.name, h.jenis FROM qurbans q
        JOIN users u ON q.user_nik = u.nik
        JOIN hewans h ON q.hewan_id = h.id
        ORDER BY q.created_at DESC");

    while ($row = mysqli_fetch_assoc($qurban)) {
        echo "<tr>
            <td>$no</td>
            <td>{$row['user_nik']}</td>
            <td>{$row['name']}</td>
            <td>{$row['jenis']}</td>
            <td>{$row['jumlah']}</td>
            <td>" . strtoupper($row['status_bayar']) . "</td>
            <td>";
        if ($row['status_bayar'] == 'belum') {
            echo "<a href='qurban_list.php?bayar=true&id={$row['id']}'>Set Sudah Bayar</a>";
        } else {
            echo "-";
        }
        echo "</td></tr>";
        $no++;
    }
    ?>
</table>