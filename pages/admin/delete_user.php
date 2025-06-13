<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";

$nik = $_GET['nik'];
mysqli_query($conn, "DELETE FROM users WHERE nik='$nik'");

header("Location: user_list.php");
exit;
