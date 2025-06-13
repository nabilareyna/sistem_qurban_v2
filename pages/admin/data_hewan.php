<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";

// Handle tambah hewan
if (isset($_POST['tambah'])) {
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];

    $query = mysqli_query($conn, "INSERT INTO hewans (jenis, harga, created_at) VALUES ('$jenis', '$harga', NOW())");
    if ($query) {
        $success = "Hewan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan hewan.";
    }
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM hewans WHERE id=$id");
    header("Location: data_hewan.php");
    exit;
}
?>

<h2>Kelola Hewan Qurban</h2>
<a href="user_list.php">← Kembali</a> | <a href="../../logout.php">Logout</a>

<?php if (isset($success))
    echo "<p style='color:green;'>$success</p>"; ?>
<?php if (isset($error))
    echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    Jenis:
    <select name="jenis" required>
        <option value="">--Pilih--</option>
        <option value="sapi">Sapi</option>
        <option value="kambing">Kambing</option>
    </select>
    Harga: <input type="number" name="harga" required>
    <button type="submit" name="tambah">Tambah</button>
</form>

<br><br>
<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Jenis</th>
        <th>Harga</th>
        <th>Waktu Input</th>
        <th>Aksi</th>
    </tr>
    <?php
    $no = 1;
    $result = mysqli_query($conn, "SELECT * FROM hewans ORDER BY created_at DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= ucfirst($row['jenis']) ?></td>
            <td><?= number_format($row['harga']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a href="data_hewan.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus hewan ini?')">Hapus</a>
            </td>
        </tr>
    <?php } ?>
</table>