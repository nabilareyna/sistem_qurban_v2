<?php
session_start();
if ($_SESSION['role'] != 'panitia') {
    header("Location: ../../index.php");
    exit;
}
?>
<h2>Selamat datang, <?= $_SESSION['nama'] ?> (Panitia)</h2>
<a href="../../logout.php">Logout</a>
