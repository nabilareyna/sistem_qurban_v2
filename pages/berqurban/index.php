<?php
session_start();
if ($_SESSION['role'] != 'berqurban') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_SESSION['nik'];

// Get user's qurban data
$qurban_query = mysqli_query($conn, "SELECT q.*, h.jenis, h.harga FROM qurbans q 
    JOIN hewans h ON q.hewan_id = h.id WHERE q.user_nik='$nik'");
$qurban_data = mysqli_fetch_assoc($qurban_query);

// Get distribution data
$distribusi_query = mysqli_query($conn, "SELECT * FROM distribusi WHERE user_nik='$nik'");
$distribusi_data = mysqli_fetch_assoc($distribusi_query);

// Calculate payment details
$payment_details = null;
if ($qurban_data) {
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

// Get qurban statistics
$stats = [];
$stats['total_berqurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='berqurban'"))['total'];
$stats['total_qurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='sudah'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Berqurban - Sistem Qurban</title>
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
                        <a class="nav-link" href="kartu_qr.php">
                            <i class="fas fa-qrcode me-1"></i>Kartu QR Saya
                            <?php if ($distribusi_data && $distribusi_data['status_ambil'] == 'belum'): ?>
                                <span class="badge bg-warning ms-1">!</span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Role Indicator -->
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-berqurban">
                                <i class="fas fa-user-check me-1"></i>BERQURBAN
                            </span>
                        </span>
                    </li>
                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold"><?= $_SESSION['nama'] ?></div>
                                <small class="text-muted">Berqurban</small>
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
                                <a class="dropdown-item" href="kartu_qr.php">
                                    <i class="fas fa-qrcode me-2"></i>Kartu QR Saya
                                </a>
                            </li>
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
                                    <i class="fas fa-hand-peace me-2"></i>Barakallahu fiikum, <?= $_SESSION['nama'] ?>!
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Terima kasih telah berpartisipasi dalam ibadah qurban</p>
                                <div class="mt-2">
                                    <span class="badge bg-white text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Status: Berqurban
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-white-50">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <?= date('l, d F Y') ?>
                                </div>
                                <div class="text-white-50 mt-1">
                                    <i class="fas fa-mosque me-2"></i>
                                    Qurban 1445 H
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Qurban Details -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Detail Qurban Anda
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($qurban_data): ?>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-<?= $qurban_data['jenis'] == 'sapi' ? 'cow' : 'sheep' ?> fa-2x text-success"></i>
                                            </div>
                                            <h5 class="card-title">Jenis Hewan</h5>
                                            <p class="card-text fs-4 fw-bold text-success"><?= ucfirst($qurban_data['jenis']) ?></p>
                                            <?php if ($qurban_data['jenis'] == 'sapi'): ?>
                                                <small class="text-muted">Patungan <?= $qurban_data['jumlah'] ?> orang</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                                            </div>
                                            <h5 class="card-title">Total Pembayaran</h5>
                                            <p class="card-text fs-4 fw-bold text-primary">Rp <?= number_format($payment_details['total_bayar']) ?></p>
                                            <span class="badge bg-success">Lunas</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Rincian Pembayaran:</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td>Harga Hewan (<?= ucfirst($qurban_data['jenis']) ?>)</td>
                                            <td class="text-end">Rp <?= number_format($qurban_data['harga']) ?></td>
                                        </tr>
                                        <?php if ($qurban_data['jenis'] == 'sapi'): ?>
                                        <tr>
                                            <td>Bagian Anda (<?= $qurban_data['jumlah'] ?>/7)</td>
                                            <td class="text-end">Rp <?= number_format($payment_details['harga_per_user']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td>Biaya Administrasi</td>
                                            <td class="text-end">Rp <?= number_format($payment_details['biaya_admin']) ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="fw-bold">Total</td>
                                            <td class="text-end fw-bold">Rp <?= number_format($payment_details['total_bayar']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h5>Data Qurban Tidak Ditemukan</h5>
                                <p>Silakan hubungi administrator untuk informasi lebih lanjut</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- QR Code & Distribution Status -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-qrcode me-2"></i>Status Pengambilan Daging
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if ($distribusi_data): ?>
                            <div class="mb-4">
                                <?php if ($distribusi_data['status_ambil'] == 'belum'): ?>
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-clock fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="text-warning">Menunggu Pengambilan</h5>
                                    <p class="text-muted">Daging Anda siap untuk diambil</p>
                                <?php else: ?>
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-check-circle fa-3x text-success"></i>
                                    </div>
                                    <h5 class="text-success">Sudah Diambil</h5>
                                    <p class="text-muted">Daging telah diambil</p>
                                <?php endif; ?>
                            </div>

                            <div class="bg-light rounded p-3 mb-3">
                                <div class="row text-center">
                                    <div class="col-12 mb-2">
                                        <strong>Jumlah Daging</strong>
                                    </div>
                                    <div class="col-12">
                                        <span class="fs-3 fw-bold text-primary"><?= $distribusi_data['jumlah_daging'] ?></span>
                                        <span class="text-muted">gram</span>
                                    </div>
                                </div>
                            </div>

                            <a href="kartu_qr.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-qrcode me-2"></i>Lihat Kartu QR
                            </a>

                            <?php if ($distribusi_data['status_ambil'] == 'belum'): ?>
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>Tunjukkan QR code kepada panitia saat pengambilan daging</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-hourglass-half fa-3x text-info"></i>
                            </div>
                            <h5 class="text-info">Menunggu Distribusi</h5>
                            <p class="text-muted">Pembagian daging belum diatur oleh admin</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information & Statistics -->
        <div class="row">
            <!-- Qurban Information -->
            <div class="col-lg-8 mb-4">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Pelaksanaan Qurban
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Hari Penyembelihan</div>
                                        <div class="text-muted">10 Dzulhijjah 1445 H</div>
                                        <div class="text-muted">17 Juni 2024</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-clock text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Waktu Penyembelihan</div>
                                        <div class="text-muted">Setelah Shalat Ied</div>
                                        <div class="text-muted">± 08:00 WIB</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Lokasi</div>
                                        <div class="text-muted">Lapangan Desa</div>
                                        <div class="text-muted">Jl. Raya Desa No. 123</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-hand-holding-heart text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Pembagian Daging</div>
                                        <div class="text-muted">Mulai pukul 10:00 WIB</div>
                                        <div class="text-muted">Tunjukkan QR Code</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
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
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <div class="stat-number text-success"><?= $stats['total_berqurban'] ?></div>
                                    <div class="stat-label">Total Berqurban</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="fas fa-clipboard-check fa-2x text-primary mb-2"></i>
                                    <div class="stat-number text-primary"><?= $stats['total_qurban'] ?></div>
                                    <div class="stat-label">Qurban Terlaksana</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
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
                                <a href="kartu_qr.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-qrcode me-2"></i>Lihat Kartu QR
                                </a>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success w-100 d-flex align-items-center justify-content-center" 
                                        onclick="showContactInfo()">
                                    <i class="fas fa-phone me-2"></i>Kontak Panitia
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info w-100 d-flex align-items-center justify-content-center" 
                                        onclick="showQurbanInfo()">
                                    <i class="fas fa-info-circle me-2"></i>Info Qurban
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showContactInfo() {
            alert('Kontak Panitia Qurban:\nTelp: 0812-3456-7890\nEmail: panitia@qurban.com\nWhatsApp: 0812-3456-7890');
        }

        function showQurbanInfo() {
            alert('Informasi Qurban:\n\nPenyembelihan: 17 Juni 2024, 08:00 WIB\nPembagian Daging: 17 Juni 2024, 10:00 WIB\nLokasi: Lapangan Desa\n\nJangan lupa bawa QR code saat pengambilan daging');
        }
    </script>
</body>
</html>
