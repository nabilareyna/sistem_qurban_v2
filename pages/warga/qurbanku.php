<?php
session_start();
if ($_SESSION['role'] != 'warga') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_SESSION['nik'];

if (isset($_POST['daftar'])) {
    $hewan_id = $_POST['hewan_id'];
    $jumlah = $_POST['jumlah'];
    $cek = mysqli_query($conn, "SELECT * FROM qurbans WHERE user_nik='$nik'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Kamu sudah terdaftar untuk qurban.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO qurbans (user_nik, hewan_id, jumlah, status_bayar, created_at) VALUES ('$nik', '$hewan_id', '$jumlah', 'belum', NOW())");
        $success = $insert ? "Pendaftaran berhasil. Silakan lakukan pembayaran." : "Gagal menyimpan data.";
    }
}

// Ambil data qurban user ini
$dataqurban = mysqli_query($conn, "SELECT q.*, h.jenis, h.harga FROM qurbans q JOIN hewans h ON q.hewan_id = h.id WHERE user_nik='$nik'");
$q = mysqli_fetch_assoc($dataqurban);
?>

<h2>Qurban Saya</h2>
<a href="../../logout.php">Logout</a><br><br>

<?php if (isset($error))
    echo "<p style='color:red;'>$error</p>"; ?>
<?php if (isset($success))
    echo "<p style='color:green;'>$success</p>"; ?>

<?php if (!$q) { ?>
    <form method="POST">
        <label>Jenis Hewan:</label>
        <select name="hewan_id" required onchange="this.form.submit()">
            <option value="">--Pilih--</option>
            <?php
            $hewans = mysqli_query($conn, "SELECT * FROM hewans");
            while ($h = mysqli_fetch_assoc($hewans)) {
                echo "<option value='{$h['id']}'";
                if (isset($_POST['hewan_id']) && $_POST['hewan_id'] == $h['id'])
                    echo " selected";
                echo ">" . ucfirst($h['jenis']) . " - Rp " . number_format($h['harga']) . "</option>";
            }
            ?>
        </select><br><br>

        <?php
        if (isset($_POST['hewan_id'])) {
            $id = $_POST['hewan_id'];
            $jenis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jenis FROM hewans WHERE id='$id'"))['jenis'];
            if ($jenis == 'sapi') {
                echo 'Jumlah Patungan (1-7 orang): <input type="number" name="jumlah" min="1" max="7" required><br><br>';
            } else {
                echo '<input type="hidden" name="jumlah" value="1">';
            }
            echo '<button type="submit" name="daftar">Daftar Qurban</button>';
        }
        ?>
    </form>
<?php } ?>

<?php if ($q) { 
    $biaya_admin = 100000;
    $total = $q['harga'] / ($q['jenis'] == 'sapi' ? 7 : 1) * $q['jumlah'] + $biaya_admin;
?>
<table border="1" cellpadding="10">
    <tr><th>Jenis</th><td><?= ucfirst($q['jenis']) ?></td></tr>
    <tr><th>Harga Hewan</th><td>Rp <?= number_format($q['harga']) ?></td></tr>
    <tr><th>Jumlah Patungan</th><td><?= $q['jumlah'] ?></td></tr>
    <tr><th>Biaya Admin</th><td>Rp <?= number_format($biaya_admin) ?></td></tr>
    <tr><th>Total Bayar</th><td><b>Rp <?= number_format($total) ?></b></td></tr>
    <tr><th>Status Bayar</th><td><b><?= strtoupper($q['status_bayar']) ?></b></td></tr>
</table>
<?php } ?>