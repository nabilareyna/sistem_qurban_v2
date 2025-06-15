<?php
session_start();
if ($_SESSION['role'] != 'warga') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_SESSION['nik'];

// Get user's qurban status
$qurban_query = mysqli_query($conn, "SELECT q.*, h.jenis, h.harga FROM qurbans q 
    LEFT JOIN hewans h ON q.hewan_id = h.id WHERE q.user_nik='$nik'");
$qurban_data = mysqli_fetch_assoc($qurban_query);
$has_qurban = $qurban_data ? true : false;

// Get available animals
$hewans = mysqli_query($conn, "SELECT * FROM hewans ORDER BY jenis ASC");

// Get user's distribution status
$distribusi_query = mysqli_query($conn, "SELECT * FROM distribusi WHERE user_nik='$nik'");
$distribusi_data = mysqli_fetch_assoc($distribusi_query);

// Calculate payment details if user has qurban
$payment_details = null;
if ($has_qurban) {
    $biaya_admin = 100000;
    $harga_per_user = $qurban_data['jenis'] == 'sapi' 
        ? ($qurban_data['harga'] / 7) * $qurban_data['jumlah']
        : $qurban_data['harga'];
    $total_bayar = $harga_per_user + $biaya_admin;
    
    $payment_details = [
        'harga_hewan' => $qurban_data['harga'],
        'harga_per_user' => $harga_per_user,
        'biaya_admin' => $biaya_admin,
        'total_bayar' => $total_bayar
    ];
}

// Get qurban statistics for information
$stats = [];
$stats['total_pendaftar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans"))['total'];
$stats['sudah_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='sudah'"))['total'];
$stats['belum_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='belum'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mosque me-2"></i>Sistem Qurban
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="qurbanku.php">
                            <i class="fas fa-clipboard-list me-1"></i>Qurban Saya
                            <?php if ($has_qurban && $qurban_data['status_bayar'] == 'belum'): ?>
                                <span class="badge bg-warning ms-1">!</span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($distribusi_data): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../berqurban/kartu_qr.php">
                            <i class="fas fa-qrcode me-1"></i>Kartu QR
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <!-- Role Indicator -->
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-warga">
                                <i class="fas fa-user me-1"></i>WARGA
                            </span>
                        </span>
                    </li>
                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold"><?= $_SESSION['nama'] ?></div>
                                <small class="text-muted">Warga</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold"><?= $_SESSION['nama'] ?></div>
                                    <small class="text-muted">NIK: <?= $_SESSION['nik'] ?></small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="qurbanku.php">
                                    <i class="fas fa-clipboard-list me-2"></i>Qurban Saya
                                </a>
                            </li>
                            <?php if ($distribusi_data): ?>
                            <li>
                                <a class="dropdown-item" href="../berqurban/kartu_qr.php">
                                    <i class="fas fa-qrcode me-2"></i>Kartu QR Saya
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="../../logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card fade-in-up">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="text-white mb-2">
                                    <i class="fas fa-hand-peace me-2"></i>Assalamu'alaikum, <?= $_SESSION['nama'] ?>!
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Selamat datang di Sistem Manajemen Qurban</p>
                                <div class="mt-2">
                                    <span class="badge bg-white text-info px-3 py-2">
                                        <i class="fas fa-user me-1"></i>Status: Warga
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-white-50">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <?= date('l, d F Y') ?>
                                </div>
                                <div class="text-white-50 mt-1">
                                    <i class="fas fa-clock me-2"></i>
                                    <?= date('H:i') ?> WIB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Status Qurban -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Status Qurban Anda
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($has_qurban): ?>
                            <div class="text-center mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-check-circle fa-3x text-success"></i>
                                </div>
                                <h4 class="text-success">Sudah Terdaftar</h4>
                                <p class="text-muted">Anda sudah terdaftar untuk qurban tahun ini</p>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="fw-bold text-primary">Jenis Hewan</div>
                                        <div class="fs-5"><?= ucfirst($qurban_data['jenis']) ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="fw-bold text-primary">Status Bayar</div>
                                        <span class="badge bg-<?= $qurban_data['status_bayar'] == 'sudah' ? 'success' : 'warning' ?> fs-6">
                                            <?= ucfirst($qurban_data['status_bayar']) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if ($qurban_data['jenis'] == 'sapi'): ?>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="fw-bold text-primary">Jumlah Patungan</div>
                                        <div class="fs-5"><?= $qurban_data['jumlah'] ?> orang</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="fw-bold text-primary">Total Bayar</div>
                                        <div class="fs-5 text-success">Rp <?= number_format($payment_details['total_bayar']) ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="qurbanku.php" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>Lihat Detail Lengkap
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-exclamation-circle fa-3x text-warning"></i>
                                </div>
                                <h4 class="text-warning">Belum Terdaftar</h4>
                                <p class="text-muted mb-4">Anda belum mendaftar untuk qurban tahun ini</p>
                                <a href="qurbanku.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Daftar Qurban Sekarang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informasi Qurban -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Pelaksanaan Qurban
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Waktu Pelaksanaan</div>
                                        <div class="text-muted">10 Dzulhijjah 1445 H</div>
                                        <div class="text-muted">17 Juni 2024</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Lokasi Penyembelihan</div>
                                        <div class="text-muted">Lapangan Desa</div>
                                        <div class="text-muted">Jl. Raya Desa No. 123</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Jadwal Kegiatan</div>
                                        <div class="text-muted">Penyembelihan: 08:00 WIB</div>
                                        <div class="text-muted">Pembagian Daging: 10:00 WIB</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-hand-holding-heart text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Pengambilan Daging</div>
                                        <div class="text-muted">Tunjukkan QR Code</div>
                                        <div class="text-muted">Mulai pukul 10:00 WIB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics & Available Animals -->
        <div class="row">
            <!-- Qurban Statistics -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Statistik Qurban
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3">
                            <div class="col-12">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <div class="stat-number text-primary"><?= $stats['total_pendaftar'] ?></div>
                                    <div class="stat-label">Total Pendaftar</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <div class="stat-number text-success"><?= $stats['sudah_bayar'] ?></div>
                                    <div class="stat-label">Sudah Bayar</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <div class="stat-number text-warning"><?= $stats['belum_bayar'] ?></div>
                                    <div class="stat-label">Belum Bayar</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Animals -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cow me-2"></i>Hewan Qurban Tersedia
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php while ($hewan = mysqli_fetch_assoc($hewans)): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100">
                                    <div class="card-body text-center">
                                        <div class="bg-<?= $hewan['jenis'] == 'sapi' ? 'warning' : 'success' ?> bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-<?= $hewan['jenis'] == 'sapi' ? 'cow' : 'sheep' ?> fa-2x text-<?= $hewan['jenis'] == 'sapi' ? 'warning' : 'success' ?>"></i>
                                        </div>
                                        <h5 class="card-title"><?= ucfirst($hewan['jenis']) ?></h5>
                                        <div class="fs-4 fw-bold text-primary mb-2">
                                            Rp <?= number_format($hewan['harga']) ?>
                                        </div>
                                        <?php if ($hewan['jenis'] == 'sapi'): ?>
                                            <small class="text-muted">Dapat dipatungan 1-7 orang</small><br>
                                            <small class="text-success">Mulai dari Rp <?= number_format($hewan['harga'] / 7) ?>/orang</small>
                                        <?php else: ?>
                                            <small class="text-muted">Untuk 1 orang</small><br>
                                            <small class="text-success">Harga tetap</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php if (!$has_qurban): ?>
                        <div class="text-center mt-3">
                            <a href="qurbanku.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Pilih & Daftar Sekarang
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if ($has_qurban): ?>
        <div class="row">
            <div class="col-12">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="qurbanku.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-eye me-2"></i>Lihat Detail Qurban
                                </a>
                            </div>
                            <?php if ($qurban_data['status_bayar'] == 'sudah' && $distribusi_data): ?>
                            <div class="col-md-4">
                                <a href="../berqurban/kartu_qr.php" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-qrcode me-2"></i>Lihat Kartu QR
                                </a>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <button class="btn btn-info w-100 d-flex align-items-center justify-content-center" onclick="showContactInfo()">
                                    <i class="fas fa-phone me-2"></i>Kontak Panitia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showContactInfo() {
            alert('Kontak Panitia:\nTelp: 0812-3456-7890\nEmail: panitia@qurban.com');
        }
    </script>
</body>
</html>
