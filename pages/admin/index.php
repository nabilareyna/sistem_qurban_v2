<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
?>
<h2>Selamat datang, <?= $_SESSION['nama'] ?> (Admin)</h2>
<a href="../../logout.php">Logout</a>
