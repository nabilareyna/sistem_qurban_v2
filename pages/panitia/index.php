<?php
session_start();
if ($_SESSION['role'] != 'panitia') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Get distribution statistics
$stats = [];
$stats['total_distribusi'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi"))['total'];
$stats['sudah_diambil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi WHERE status_ambil='diambil'"))['total'];
$stats['belum_diambil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi WHERE status_ambil='belum'"))['total'];

// Get qurban statistics
$stats['total_qurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans"))['total'];
$stats['qurban_sudah_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='sudah'"))['total'];

// Recent scan activities (if any)
// $recent_scans = mysqli_query($conn, "SELECT d.*, u.name FROM distribusi d 
//     JOIN users u ON d.user_nik = u.nik 
//     WHERE d.status_ambil = 'diambil' 
//     ORDER BY d.updated_at DESC LIMIT 5");

// Pending distributions
$pending_distributions = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d 
    JOIN users u ON d.user_nik = u.nik 
    WHERE d.status_ambil = 'belum' 
    ORDER BY u.role DESC, u.name ASC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia - Sistem Qurban</title>
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
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="scan_qr.php">
                            <i class="fas fa-qrcode me-1"></i>Scan QR Code
                            <?php if ($stats['belum_diambil'] > 0): ?>
                                <span class="badge bg-warning ms-1"><?= $stats['belum_diambil'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Role Indicator -->
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-panitia">
                                <i class="fas fa-user-tie me-1"></i>PANITIA
                            </span>
                        </span>
                    </li>
                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold"><?= $_SESSION['nama'] ?></div>
                                <small class="text-muted">Panitia</small>
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
                                <a class="dropdown-item" href="scan_qr.php">
                                    <i class="fas fa-qrcode me-2"></i>Scan QR Code
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
                                    <i class="fas fa-hand-peace me-2"></i>Selamat Datang, <?= $_SESSION['nama'] ?>!
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Dashboard Panitia Distribusi Daging Qurban</p>
                                <div class="mt-2">
                                    <span class="badge bg-white text-warning px-3 py-2">
                                        <i class="fas fa-user-tie me-1"></i>Panitia Distribusi
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-hand-holding-heart fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= number_format($stats['total_distribusi']) ?></div>
                        <div class="stat-label">Total Distribusi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= number_format($stats['sudah_diambil']) ?></div>
                        <div class="stat-label">Sudah Diambil</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="stat-number text-warning"><?= number_format($stats['belum_diambil']) ?></div>
                        <div class="stat-label">Belum Diambil</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-percentage fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-info"><?= $stats['total_distribusi'] > 0 ? round(($stats['sudah_diambil'] / $stats['total_distribusi']) * 100) : 0 ?>%</div>
                        <div class="stat-label">Progress Distribusi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- QR Scanner Section -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-qrcode me-2"></i>Scanner QR Code
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                            <i class="fas fa-qrcode fa-4x text-primary"></i>
                        </div>
                        <h4 class="mb-3">Scan QR Code Penerima</h4>
                        <p class="text-muted mb-4">Gunakan scanner untuk memverifikasi dan mencatat pengambilan daging qurban</p>
                        <a href="scan_qr.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-camera me-2"></i>Mulai Scan QR
                        </a>
                        <?php if ($stats['belum_diambil'] > 0): ?>
                        <div class="mt-3">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Ada <strong><?= $stats['belum_diambil'] ?></strong> distribusi yang belum diambil
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Distribution Progress -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Progress Distribusi
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($stats['total_distribusi'] > 0): ?>
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <div class="progress mx-auto" style="width: 150px; height: 150px; border-radius: 50%;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?= ($stats['sudah_diambil'] / $stats['total_distribusi']) * 100 ?>%; height: 100%; border-radius: 50%;">
                                        </div>
                                    </div>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <div class="fs-3 fw-bold text-success">
                                            <?= round(($stats['sudah_diambil'] / $stats['total_distribusi']) * 100) ?>%
                                        </div>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row text-center g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <div class="fw-bold text-success">Sudah Diambil</div>
                                        <div class="fs-4 fw-bold text-success"><?= $stats['sudah_diambil'] ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <div class="fw-bold text-warning">Belum Diambil</div>
                                        <div class="fs-4 fw-bold text-warning"><?= $stats['belum_diambil'] ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-4x mb-3"></i>
                                <h5>Belum Ada Data Distribusi</h5>
                                <p>Data distribusi akan muncul setelah admin mengatur pembagian daging</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Pending Distributions -->
        <div class="row">
            <!-- Recent Scan Activities -->
            

            <!-- Pending Distributions -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 fade-in-up">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Menunggu Pengambilan
                        </h5>
                        <span class="badge bg-warning"><?= mysqli_num_rows($pending_distributions) ?> pending</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pending_distributions) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($pending = mysqli_fetch_assoc($pending_distributions)): ?>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-warning"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?= $pending['name'] ?></div>
                                                    <small class="text-muted">
                                                        NIK: <?= $pending['user_nik'] ?> • 
                                                        <?= $pending['jumlah_daging'] ?>gr
                                                    </small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-<?= 
                                                            $pending['role'] == 'berqurban' ? 'success' : 
                                                            ($pending['role'] == 'warga' ? 'info' : 'secondary') 
                                                        ?> badge-sm">
                                                            <?= ucfirst($pending['role']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php if ($stats['belum_diambil'] > 10): ?>
                                <div class="text-center mt-3">
                                    <small class="text-muted">Dan <?= $stats['belum_diambil'] - 10 ?> lainnya...</small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <p>Semua distribusi sudah diambil!</p>
                            </div>
                        <?php endif; ?>
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
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat Panitia
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="scan_qr.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-qrcode me-2"></i>Scan QR Code
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info w-100 d-flex align-items-center justify-content-center" 
                                        onclick="showHelp()">
                                    <i class="fas fa-question-circle me-2"></i>Bantuan
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
        function showHelp() {
            alert('Cara menggunakan sistem:\n\n1. Scan QR Code: Gunakan menu "Scan QR" untuk memverifikasi pengambilan daging\n2. Verifikasi Token: Masukkan token QR yang ditunjukkan penerima\n3. Konfirmasi: Sistem akan mencatat pengambilan secara otomatis\n\nHubungi admin jika ada masalah teknis');
        }
    </script>
</body>
</html>
