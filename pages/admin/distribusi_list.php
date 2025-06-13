<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";
?>

<h2>Data Distribusi Daging</h2>
<a href="hitung_distribusi.php">🔁 Hitung Otomatis Lagi</a> | 
<a href="../../logout.php">Logout</a><br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Role</th>
        <th>Jumlah Daging (gram)</th>
        <th>Status</th>
        <th>Token</th>
    </tr>
    <?php
    $no = 1;
    $result = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d JOIN users u ON d.user_nik = u.nik ORDER BY u.role DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>" . $no++ . "</td>
            <td>{$row['user_nik']}</td>
            <td>{$row['name']}</td>
            <td>{$row['role']}</td>
            <td>{$row['jumlah_daging']} gr</td>
            <td>{$row['status_ambil']}</td>
            <td>{$row['token']}</td>
            <td><a href=\"view_qr.php?nik={$row['user_nik']}\" target=\"_blank\">Lihat QR</a></td>
        </tr>";
    }
    ?>
</table>
