<?php
session_start();
if ($_SESSION['role'] != 'warga') {
    header("Location: ../../index.php");
    exit;
}
?>
<h2>Selamat datang, <?= $_SESSION['nama'] ?> (<?= $_SESSION['role'] ?>)</h2>
<a href="../../logout.php">Logout</a>