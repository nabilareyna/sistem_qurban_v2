<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";

$totalDaging = 300000;

$BOBOT = [
    'berqurban' => 4,
    'panitia' => 3,
    'warga' => 1
];

$users = mysqli_query($conn, "SELECT nik, role FROM users WHERE role != 'admin'");
$userList = [];
$totalBobot = 0;

while ($u = mysqli_fetch_assoc($users)) {
    $bobot = isset($BOBOT[$u['role']]) ? $BOBOT[$u['role']] : 0;
    $userList[] = [
        'nik' => $u['nik'],
        'role' => $u['role'],
        'bobot' => $bobot
    ];
    $totalBobot += $bobot;
}

//ngitung daging per bobot
$dagingPerBobot = $totalBobot > 0 ? $totalDaging / $totalBobot : 0;

mysqli_query($conn, "DELETE FROM distribusi");


foreach ($userList as $user) {
    $jatah = round($user['bobot'] * $dagingPerBobot);
    if ($jatah > $totalDaging) {
        $jatah = $totalDaging;
    }

    $token = bin2hex(random_bytes(10));

    mysqli_query($conn, "INSERT INTO distribusi (user_nik, jumlah_daging, status_ambil, token, created_at) VALUES (
        '{$user['nik']}', '$jatah', 'belum', '$token', NOW()
    )");

    $totalDaging -= $jatah;
}

echo "<p style='color:green;'>Distribusi berhasil dihitung dan disimpan!</p>";
echo "<a href='distribusi_list.php'>Lihat Data Distribusi</a>";
