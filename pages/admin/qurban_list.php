<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Handle update status and role
if (isset($_GET['bayar']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data qurban
    $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM qurbans WHERE id='$id'"));
    $user_nik = $q['user_nik'];
    $hewan_id = $q['hewan_id'];
    $jumlah = $q['jumlah'];

    // Ambil harga hewan
    $hewan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hewans WHERE id='$hewan_id'"));
    $harga_per_user = $hewan['jenis'] == 'sapi'
        ? ($hewan['harga'] / 7) * $jumlah
        : $hewan['harga'];

    // Biaya admin (fix: 100.000)
    $biaya_admin = 100000;
    $total = $harga_per_user + $biaya_admin;

    // Update status bayar
    mysqli_query($conn, "UPDATE qurbans SET status_bayar='sudah' WHERE id='$id'");

    // Ubah role user menjadi 'berqurban'
    mysqli_query($conn, "UPDATE users SET role='berqurban' WHERE nik='$user_nik'");

    // Catat pembayaran qurban
    mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        'masuk',
        'pembayaran qurban',
        '$harga_per_user',
        'User NIK $user_nik bayar qurban ($hewan[jenis])',
        NOW())");

    // Catat biaya administrasi
    mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        'masuk',
        'biaya administrasi',
        '$biaya_admin',
        'Biaya admin dari user NIK $user_nik',
        NOW())");

    $success = "Status pembayaran berhasil diperbarui dan user telah menjadi berqurban!";
}

// Get statistics
$stats = [];
$stats['total_qurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans"))['total'];
$stats['sudah_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='sudah'"))['total'];
$stats['belum_bayar'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans WHERE status_bayar='belum'"))['total'];
$stats['total_sapi'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans q JOIN hewans h ON q.hewan_id = h.id WHERE h.jenis='sapi'"))['total'];
$stats['total_kambing'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM qurbans q JOIN hewans h ON q.hewan_id = h.id WHERE h.jenis='kambing'"))['total'];

// Calculate total revenue
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keuangan WHERE kategori IN ('pembayaran qurban', 'biaya administrasi')"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Qurban - Sistem Qurban</title>
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
                        <a class="nav-link active" href="qurban_list.php">
                            <i class="fas fa-list me-1"></i>Data Qurban
                            <?php if ($stats['belum_bayar'] > 0): ?>
                                <span class="badge bg-warning ms-1"><?= $stats['belum_bayar'] ?></span>
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
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <span class="role-badge role-admin">
                                <i class="fas fa-user-shield me-1"></i>ADMINISTRATOR
                            </span>
                        </span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
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
                                    <i class="fas fa-list me-2"></i>Data Qurban Warga
                                </h2>
                                <p class="text-white-50 mb-0">Manajemen pendaftaran dan pembayaran qurban</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group">
                                    <a href="print_qr.php" class="btn btn-light">
                                        <i class="fas fa-qrcode me-2"></i>Cetak QR
                                    </a>
                                    <button class="btn btn-light" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Print
                                    </button>
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
                                <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $stats['total_qurban'] ?></div>
                        <div class="stat-label">Total Pendaftar</div>
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
                        <div class="stat-number text-success"><?= $stats['sudah_bayar'] ?></div>
                        <div class="stat-label">Sudah Bayar</div>
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
                        <div class="stat-number text-warning"><?= $stats['belum_bayar'] ?></div>
                        <div class="stat-label">Belum Bayar</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-cow fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="stat-number text-warning"><?= $stats['total_sapi'] ?></div>
                        <div class="stat-label">Qurban Sapi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-sheep fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= $stats['total_kambing'] ?></div>
                        <div class="stat-label">Qurban Kambing</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-info">Rp <?= number_format($total_revenue) ?></div>
                        <div class="stat-label">Total Pendapatan</div>
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

        <!-- Qurban Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Pendaftar Qurban
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?filter=all">Semua Status</a></li>
                                    <li><a class="dropdown-item" href="?filter=sudah">Sudah Bayar</a></li>
                                    <li><a class="dropdown-item" href="?filter=belum">Belum Bayar</a></li>
                                </ul>
                            </div>
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
                                        <th width="15%">
                                            <i class="fas fa-paw me-2"></i>Jenis Hewan
                                        </th>
                                        <th width="10%">
                                            <i class="fas fa-users me-2"></i>Jumlah
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-money-bill-wave me-2"></i>Total Bayar
                                        </th>
                                        <th width="12%">
                                            <i class="fas fa-credit-card me-2"></i>Status Bayar
                                        </th>
                                        <th width="11%">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $filter = $_GET['filter'] ?? 'all';
                                    $where_clause = "";
                                    if ($filter == 'sudah') {
                                        $where_clause = "WHERE q.status_bayar = 'sudah'";
                                    } elseif ($filter == 'belum') {
                                        $where_clause = "WHERE q.status_bayar = 'belum'";
                                    }

                                    $qurban = mysqli_query($conn, "SELECT q.*, u.name, h.jenis, h.harga FROM qurbans q
                                        JOIN users u ON q.user_nik = u.nik
                                        JOIN hewans h ON q.hewan_id = h.id
                                        $where_clause
                                        ORDER BY q.created_at DESC");

                                    if (mysqli_num_rows($qurban) > 0):
                                        while ($row = mysqli_fetch_assoc($qurban)):
                                            // Calculate total payment
                                            $biaya_admin = 100000;
                                            $harga_per_user = $row['jenis'] == 'sapi'
                                                ? ($row['harga'] / 7) * $row['jumlah']
                                                : $row['harga'];
                                            $total_bayar = $harga_per_user + $biaya_admin;
                                            ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <div class="fw-semibold"><?= $row['user_nik'] ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold"><?= $row['name'] ?></div>
                                                            <small class="text-muted">Terdaftar:
                                                                <?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="bg-<?= $row['jenis'] == 'sapi' ? 'warning' : 'success' ?> bg-opacity-10 rounded-circle p-2 me-2">
                                                            <i
                                                                class="fas fa-<?= $row['jenis'] == 'sapi' ? 'cow' : 'sheep' ?> text-<?= $row['jenis'] == 'sapi' ? 'warning' : 'success' ?>"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold"><?= ucfirst($row['jenis']) ?></div>
                                                            <small class="text-muted">Rp
                                                                <?= number_format($row['harga']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($row['jenis'] == 'sapi'): ?>
                                                        <span class="badge bg-warning"><?= $row['jumlah'] ?> orang</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">1 orang</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary">Rp <?= number_format($total_bayar) ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        Hewan: Rp <?= number_format($harga_per_user) ?><br>
                                                        Admin: Rp <?= number_format($biaya_admin) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?= $row['status_bayar'] == 'sudah' ? 'success' : 'warning' ?> fs-6">
                                                        <i
                                                            class="fas fa-<?= $row['status_bayar'] == 'sudah' ? 'check-circle' : 'clock' ?> me-1"></i>
                                                        <?= strtoupper($row['status_bayar']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($row['status_bayar'] == 'belum'): ?>
                                                        <a href="qurban_list.php?bayar=true&id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Konfirmasi pembayaran untuk <?= $row['name'] ?>?')">
                                                            <i class="fas fa-check me-1"></i>Konfirmasi
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>Lunas
                                                        </span>
                                                    <?php endif; ?>
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
                                                    <h5>Belum Ada Pendaftar Qurban</h5>
                                                    <p>Data pendaftar qurban akan muncul di sini</p>
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
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>