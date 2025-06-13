<?php
$host = "localhost";
$user = "root";
$pass = "admin";
$db   = "sistem_informasi_qurban";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>