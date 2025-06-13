<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";
// $keyword = isset($_GET['search']) ? $_GET['search'] : '';
// $where = $keyword ? "WHERE name LIKE '%$keyword%' OR nik LIKE '%$keyword%'" : '';
// $users = mysqli_query($conn, "SELECT * FROM users $where");

// if (!$users) {
//     echo "Query error: " . mysqli_error($conn);
// }

?>

<h2>Daftar User</h2>
<a href="../../logout.php">Logout</a>
<br><br>
<!-- 
<form method="GET">
    <input type="text" name="search" placeholder="Cari nama atau NIK..." value="<?= htmlspecialchars($keyword) ?>">
    <button type="submit">Cari</button>
    <a href="user_list.php">Reset</a>
</form>
<br> -->

<table border="1" cellpadding="10">
    <tr>
        <th>NIK</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Role</th>
        <th>No HP</th>
        <th>Aksi</th>
    </tr>

    <?php
    $users = mysqli_query($conn, "SELECT * FROM users");
    while ($row = mysqli_fetch_assoc($users)) {
    ?>
    <tr>
        <td><?= $row['nik'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['username'] ?></td>
        <td><?= $row['role'] ?></td>
        <td><?= $row['no_hp'] ?></td>
        <td>
            <a href="edit_user.php?nik=<?= $row['nik'] ?>">Edit</a>
            <a href="delete_user.php?nik=<?= $row['nik'] ?>" onclick="return confirm('Yakin ingin hapus user ini?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>
