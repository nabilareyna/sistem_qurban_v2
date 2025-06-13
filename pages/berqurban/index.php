<?php
session_start();
if ($_SESSION['role'] != 'berqurban') {
    header("Location: ../../index.php");
    exit;
}
?>
<h2>Selamat datang, <?= $_SESSION['nama'] ?> (Berqurban)</h2>
<a href="../../logout.php">Logout</a>
