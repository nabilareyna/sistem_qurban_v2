<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Get comprehensive statistics
$stats = [];
$stats['total_users'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$stats['total_qurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans"))['total'];
$stats['total_hewans'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hewans"))['total'];
$stats['qurban_belum_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='belum'"))['total'];
$stats['qurban_sudah_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='sudah'"))['total'];

// Financial statistics
$stats['total_masuk'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='masuk'"))['total'] ?? 0;
$stats['total_keluar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='keluar'"))['total'] ?? 0;
$stats['saldo'] = $stats['total_masuk'] - $stats['total_keluar'];

// User role distribution
$role_stats = [];
$roles = ['admin', 'panitia', 'berqurban', 'warga'];
foreach ($roles as $role) {
    $role_stats[$role] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='$role'"))['total'];
}

// Recent activities
$recent_qurban = mysqli_query($conn, "SELECT q.*, u.name, h.jenis FROM qurbans q
    JOIN users u ON q.user_nik = u.nik
    JOIN hewans h ON q.hewan_id = h.id
    ORDER BY q.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Qurban</title>
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
                        <a class="nav-link" href="user_list.php">
                            <i class="fas fa-users me-1"></i>Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="data_hewan.php">
                            <i class="fas fa-cow me-1"></i>Kelola Hewan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="qurban_list.php">
                            <i class="fas fa-list me-1"></i>Data Qurban
                            <?php if ($stats['qurban_belum_bayar'] > 0): ?>
                                <span class="badge bg-warning ms-1"><?= $stats['qurban_belum_bayar'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="distribusi_list.php">
                            <i class="fas fa-hand-holding-heart me-1"></i>Distribusi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="keuangan.php">
                            <i class="fas fa-money-bill-wave me-1"></i>Keuangan
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Role Indicator -->
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-admin">
                                <i class="fas fa-user-shield me-1"></i>ADMINISTRATOR
                            </span>
                        </span>
                    </li>
                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold"><?= $_SESSION['nama'] ?></div>
                                <small class="text-muted">Administrator</small>
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
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="text-white mb-2">
                                    <i class="fas fa-hand-peace me-2"></i>Selamat Datang, <?= $_SESSION['nama'] ?>!
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Dashboard Administrator Sistem Manajemen Qurban</p>
                                <div class="mt-2">
                                    <span class="badge bg-white text-danger px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Login: <?= date('d/m/Y H:i') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-white-50">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <?= date('l, d F Y') ?>
                                </div>
                                <div class="text-white-50 mt-1">
                                    <i class="fas fa-user-shield me-2"></i>
                                    Administrator Panel
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= number_format($stats['total_users']) ?></div>
                        <div class="stat-label">Total User Terdaftar</div>
                        <div class="mt-2">
                            <a href="user_list.php" class="btn btn-sm btn-outline-primary">Kelola</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clipboard-list fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= number_format($stats['total_qurban']) ?></div>
                        <div class="stat-label">Total Pendaftar Qurban</div>
                        <div class="mt-2">
                            <small class="text-success">✓ <?= $stats['qurban_sudah_bayar'] ?> Sudah Bayar</small><br>
                            <small class="text-warning">⏳ <?= $stats['qurban_belum_bayar'] ?> Belum Bayar</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-cow fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="stat-number text-warning"><?= number_format($stats['total_hewans']) ?></div>
                        <div class="stat-label">Jenis Hewan Tersedia</div>
                        <div class="mt-2">
                            <a href="data_hewan.php" class="btn btn-sm btn-outline-warning">Kelola</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-wallet fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-<?= $stats['saldo'] >= 0 ? 'info' : 'danger' ?>">
                            Rp <?= number_format(abs($stats['saldo'])) ?>
                        </div>
                        <div class="stat-label">Saldo <?= $stats['saldo'] >= 0 ? 'Tersedia' : 'Minus' ?></div>
                        <div class="mt-2">
                            <a href="keuangan.php" class="btn btn-sm btn-outline-info">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- User Role Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users-cog me-2"></i>Distribusi User Role
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($role_stats as $role => $count): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-<?= $role == 'admin' ? 'danger' : ($role == 'panitia' ? 'warning' : ($role == 'berqurban' ? 'success' : 'info')) ?> bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-<?= $role == 'admin' ? 'user-shield' : ($role == 'panitia' ? 'user-tie' : ($role == 'berqurban' ? 'user-check' : 'user')) ?> text-<?= $role == 'admin' ? 'danger' : ($role == 'panitia' ? 'warning' : ($role == 'berqurban' ? 'success' : 'info')) ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= ucfirst($role) ?></div>
                                        <small class="text-muted"><?= $count ?> orang</small>
                                    </div>
                                </div>
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div class="progress-bar bg-<?= $role == 'admin' ? 'danger' : ($role == 'panitia' ? 'warning' : ($role == 'berqurban' ? 'success' : 'info')) ?>" 
                                         style="width: <?= $stats['total_users'] > 0 ? ($count / $stats['total_users']) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Ringkasan Keuangan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3">
                            <div class="col-12">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                    <div class="fw-bold text-success">Total Pemasukan</div>
                                    <div class="fs-5 fw-semibold text-success">Rp <?= number_format($stats['total_masuk']) ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                                    <div class="fw-bold text-danger">Total Pengeluaran</div>
                                    <div class="fs-5 fw-semibold text-danger">Rp <?= number_format($stats['total_keluar']) ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold text-primary">Saldo Akhir</div>
                                    <div class="fs-4 fw-bold text-<?= $stats['saldo'] >= 0 ? 'primary' : 'danger' ?>">
                                        Rp <?= number_format(abs($stats['saldo'])) ?>
                                        <?= $stats['saldo'] < 0 ? '(Minus)' : '' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="keuangan.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Aktivitas Terbaru
                        </h5>
                        <a href="qurban_list.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($recent_qurban) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($activity = mysqli_fetch_assoc($recent_qurban)): ?>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-user-plus text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold"><?= $activity['name'] ?></div>
                                                <small class="text-muted">
                                                    Mendaftar qurban <?= $activity['jenis'] ?>
                                                    <?php if ($activity['jenis'] == 'sapi'): ?>
                                                        (<?= $activity['jumlah'] ?> orang)
                                                    <?php endif; ?>
                                                </small>
                                                <div class="mt-1">
                                                    <span class="badge bg-<?= $activity['status_bayar'] == 'sudah' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($activity['status_bayar']) ?> Bayar
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Belum ada aktivitas pendaftaran</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat Administrator
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="../../register.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user-plus me-2"></i>Tambah User Baru
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="data_hewan.php" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus me-2"></i>Tambah Hewan
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="print_qr.php" class="btn btn-info w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-qrcode me-2"></i>Cetak QR Code
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="keuangan.php" class="btn btn-warning w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus me-2"></i>Input Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
