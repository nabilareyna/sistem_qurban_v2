<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Get distribution statistics
$stats = [];
$stats['total_distribusi'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi"))['total'];
$stats['sudah_diambil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi WHERE status_ambil='diambil'"))['total'];
$stats['belum_diambil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi WHERE status_ambil='belum'"))['total'];
$stats['total_daging'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_daging) as total FROM distribusi"))['total'] ?? 0;
$stats['daging_diambil'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_daging) as total FROM distribusi WHERE status_ambil='diambil'"))['total'] ?? 0;

// Handle reset distribution status
if (isset($_GET['reset']) && isset($_GET['nik'])) {
    $nik = $_GET['nik'];
    $query = mysqli_query($conn, "UPDATE distribusi SET status_ambil='belum' WHERE user_nik='$nik'");
    if ($query) {
        $success = "Status distribusi berhasil direset!";
    } else {
        $error = "Gagal mereset status distribusi!";
    }
}

// Handle delete distribution
if (isset($_GET['hapus']) && isset($_GET['nik'])) {
    $nik = $_GET['nik'];
    $query = mysqli_query($conn, "DELETE FROM distribusi WHERE user_nik='$nik'");
    if ($query) {
        $success = "Data distribusi berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data distribusi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Distribusi - Sistem Qurban</title>
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
                        <a class="nav-link" href="index.php">
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
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="distribusi_list.php">
                            <i class="fas fa-hand-holding-heart me-1"></i>Distribusi
                            <?php if ($stats['belum_diambil'] > 0): ?>
                                <span class="badge bg-warning ms-1"><?= $stats['belum_diambil'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="keuangan.php">
                            <i class="fas fa-money-bill-wave me-1"></i>Keuangan
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-admin">
                                <i class="fas fa-user-shield me-1"></i>ADMINISTRATOR
                            </span>
                        </span>
                    </li>
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
                            <li><a class="dropdown-item text-danger" href="../../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="text-white mb-2">
                                    <i class="fas fa-hand-holding-heart me-2"></i>Daftar Distribusi Daging
                                </h2>
                                <p class="text-white-50 mb-0">Manajemen distribusi dan pengambilan daging qurban</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group">
                                    <a href="hitung_distribusi.php" class="btn btn-light">
                                        <i class="fas fa-calculator me-2"></i>Hitung Distribusi
                                    </a>
                                    <a href="print_qr.php" class="btn btn-light">
                                        <i class="fas fa-qrcode me-2"></i>Cetak QR
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-hand-holding-heart fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $stats['total_distribusi'] ?></div>
                        <div class="stat-label">Total Distribusi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= $stats['sudah_diambil'] ?></div>
                        <div class="stat-label">Sudah Diambil</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="stat-number text-warning"><?= $stats['belum_diambil'] ?></div>
                        <div class="stat-label">Belum Diambil</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-weight fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-info"><?= number_format($stats['total_daging']) ?></div>
                        <div class="stat-label">Total Daging (gr)</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-balance-scale fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= number_format($stats['daging_diambil']) ?></div>
                        <div class="stat-label">Daging Diambil (gr)</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-percentage fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <div class="stat-number text-secondary">
                            <?= $stats['total_distribusi'] > 0 ? round(($stats['sudah_diambil'] / $stats['total_distribusi']) * 100) : 0 ?>%
                        </div>
                        <div class="stat-label">Progress</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Distribution Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Distribusi Daging
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filter Status
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?status=all">Semua Status</a></li>
                                    <li><a class="dropdown-item" href="?status=diambil">Sudah Diambil</a></li>
                                    <li><a class="dropdown-item" href="?status=belum">Belum Diambil</a></li>
                                </ul>
                            </div>
                            <a href="hitung_distribusi.php" class="btn btn-success btn-sm">
                                <i class="fas fa-calculator me-1"></i>Hitung Ulang
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">
                                            <i class="fas fa-id-card me-2"></i>NIK
                                        </th>
                                        <th width="20%">
                                            <i class="fas fa-user me-2"></i>Nama
                                        </th>
                                        <th width="10%">
                                            <i class="fas fa-user-tag me-2"></i>Role
                                        </th>
                                        <th width="12%">
                                            <i class="fas fa-weight me-2"></i>Jumlah Daging
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-qrcode me-2"></i>Token QR
                                        </th>
                                        <th width="12%">
                                            <i class="fas fa-check-circle me-2"></i>Status
                                        </th>
                                        <th width="14%">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $status_filter = $_GET['status'] ?? 'all';
                                    $where_clause = "";
                                    if ($status_filter == 'diambil') {
                                        $where_clause = "WHERE d.status_ambil = 'diambil'";
                                    } elseif ($status_filter == 'belum') {
                                        $where_clause = "WHERE d.status_ambil = 'belum'";
                                    }

                                    $distribusi = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d 
                                        JOIN users u ON d.user_nik = u.nik 
                                        $where_clause
                                        ORDER BY u.role DESC, d.status_ambil ASC, u.name ASC");
                                    
                                    if (mysqli_num_rows($distribusi) > 0):
                                        while ($row = mysqli_fetch_assoc($distribusi)):
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <div class="fw-semibold"><?= $row['user_nik'] ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-<?= 
                                                        $row['role'] == 'admin' ? 'danger' : 
                                                        ($row['role'] == 'panitia' ? 'warning' : 
                                                        ($row['role'] == 'berqurban' ? 'success' : 'info')) 
                                                    ?> bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="fas fa-<?= 
                                                            $row['role'] == 'admin' ? 'user-shield' : 
                                                            ($row['role'] == 'panitia' ? 'user-tie' : 
                                                            ($row['role'] == 'berqurban' ? 'user-check' : 'user')) 
                                                        ?> text-<?= 
                                                            $row['role'] == 'admin' ? 'danger' : 
                                                            ($row['role'] == 'panitia' ? 'warning' : 
                                                            ($row['role'] == 'berqurban' ? 'success' : 'info')) 
                                                        ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?= $row['name'] ?></div>
                                                        <small class="text-muted">ID: <?= $row['user_nik'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="role-badge role-<?= $row['role'] ?>">
                                                    <?= strtoupper($row['role']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary"><?= number_format($row['jumlah_daging']) ?> gr</div>
                                                <small class="text-muted">~<?= number_format($row['jumlah_daging'] / 1000, 1) ?> kg</small>
                                            </td>
                                            <td>
                                                <div class="font-monospace text-muted" style="font-size: 0.8rem;">
                                                    <?= substr($row['token'], 0, 8) ?>...
                                                </div>
                                                <a href="view_qr.php?nik=<?= $row['user_nik'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-qrcode me-1"></i>Lihat QR
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $row['status_ambil'] == 'diambil' ? 'success' : 'warning' ?> fs-6">
                                                    <i class="fas fa-<?= $row['status_ambil'] == 'diambil' ? 'check-circle' : 'clock' ?> me-1"></i>
                                                    <?= $row['status_ambil'] == 'diambil' ? 'DIAMBIL' : 'BELUM' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($row['status_ambil'] == 'diambil'): ?>
                                                        <a href="?reset=true&nik=<?= $row['user_nik'] ?>" 
                                                           class="btn btn-sm btn-warning"
                                                           onclick="return confirm('Reset status pengambilan untuk <?= $row['name'] ?>?')"
                                                           title="Reset Status">
                                                            <i class="fas fa-undo"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="view_qr.php?nik=<?= $row['user_nik'] ?>" 
                                                       class="btn btn-sm btn-info" target="_blank" title="Lihat QR">
                                                        <i class="fas fa-qrcode"></i>
                                                    </a>
                                                    <a href="?hapus=true&nik=<?= $row['user_nik'] ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Yakin ingin menghapus distribusi untuk <?= $row['name'] ?>?')"
                                                       title="Hapus Distribusi">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <h5>Belum Ada Data Distribusi</h5>
                                                    <p>Silakan hitung distribusi terlebih dahulu</p>
                                                    <a href="hitung_distribusi.php" class="btn btn-primary">
                                                        <i class="fas fa-calculator me-2"></i>Hitung Distribusi
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
