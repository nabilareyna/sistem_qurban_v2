<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Query total
$total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='masuk'"))['total'] ?? 0;
$total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='keluar'"))['total'] ?? 0;
$saldo = $total_masuk - $total_keluar;

// Get category statistics
$kategori_masuk = mysqli_query($conn, "SELECT kategori, SUM(jumlah) as total FROM keuangan WHERE tipe='masuk' GROUP BY kategori ORDER BY total DESC");
$kategori_keluar = mysqli_query($conn, "SELECT kategori, SUM(jumlah) as total FROM keuangan WHERE tipe='keluar' GROUP BY kategori ORDER BY total DESC");

// Handle tambah data keuangan
if (isset($_POST['tambah'])) {
    $tipe = $_POST['tipe'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $catatan = $_POST['catatan'];

    $query = mysqli_query($conn, "INSERT INTO keuangan (tipe, kategori, jumlah, catatan, created_at) VALUES (
        '$tipe', '$kategori', '$jumlah', '$catatan', NOW())");

    if ($query) {
        $success = "Transaksi berhasil ditambahkan!";
        // Refresh totals
        $total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='masuk'"))['total'] ?? 0;
        $total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='keluar'"))['total'] ?? 0;
        $saldo = $total_masuk - $total_keluar;
    } else {
        $error = "Gagal menambahkan transaksi: " . mysqli_error($conn);
    }
}

// Handle delete transaction
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = mysqli_query($conn, "DELETE FROM keuangan WHERE id='$id'");
    if ($query) {
        $success = "Transaksi berhasil dihapus!";
    } else {
        $error = "Gagal menghapus transaksi!";
    }
}

// Get recent transactions
$recent_transactions = mysqli_query($conn, "SELECT * FROM keuangan ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Keuangan - Sistem Qurban</title>
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
                        <a class="nav-link active" href="keuangan.php">
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
                                    <i class="fas fa-money-bill-wave me-2"></i>Kelola Keuangan
                                </h2>
                                <p class="text-white-50 mb-0">Manajemen keuangan dan transaksi qurban</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#addTransactionModal">
                                    <i class="fas fa-plus me-2"></i>Tambah Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-arrow-up fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success">Rp <?= number_format($total_masuk) ?></div>
                        <div class="stat-label">Total Pemasukan</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-arrow-down fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="stat-number text-danger">Rp <?= number_format($total_keluar) ?></div>
                        <div class="stat-label">Total Pengeluaran</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-<?= $saldo >= 0 ? 'primary' : 'warning' ?> bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-wallet fa-2x text-<?= $saldo >= 0 ? 'primary' : 'warning' ?>"></i>
                            </div>
                        </div>
                        <div class="stat-number text-<?= $saldo >= 0 ? 'primary' : 'warning' ?>">
                            Rp <?= number_format(abs($saldo)) ?>
                        </div>
                        <div class="stat-label">Saldo <?= $saldo >= 0 ? 'Tersedia' : 'Minus' ?></div>
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

        <div class="row">
            <!-- Transaction Form & Categories -->
            <div class="col-lg-4 mb-4">
                <!-- Quick Add Transaction -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Tambah Transaksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-exchange-alt me-2"></i>Tipe Transaksi
                                </label>
                                <select name="tipe" class="form-select" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="masuk">💰 Pemasukan</option>
                                    <option value="keluar">💸 Pengeluaran</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-2"></i>Kategori
                                </label>
                                <input type="text" name="kategori" class="form-control"
                                    placeholder="Contoh: Pembayaran qurban, Biaya operasional" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave me-2"></i>Jumlah (Rp)
                                </label>
                                <input type="number" name="jumlah" class="form-control" placeholder="0" required
                                    min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>Catatan
                                </label>
                                <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."
                                    required></textarea>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Kategori Transaksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-success">
                                <i class="fas fa-arrow-up me-2"></i>Pemasukan
                            </h6>
                            <?php if (mysqli_num_rows($kategori_masuk) > 0): ?>
                                <?php while ($kat = mysqli_fetch_assoc($kategori_masuk)): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><?= ucfirst($kat['kategori']) ?></small>
                                        <small class="fw-bold text-success">Rp <?= number_format($kat['total']) ?></small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small class="text-muted">Belum ada pemasukan</small>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <div>
                            <h6 class="text-danger">
                                <i class="fas fa-arrow-down me-2"></i>Pengeluaran
                            </h6>
                            <?php if (mysqli_num_rows($kategori_keluar) > 0): ?>
                                <?php while ($kat = mysqli_fetch_assoc($kategori_keluar)): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><?= ucfirst($kat['kategori']) ?></small>
                                        <small class="fw-bold text-danger">Rp <?= number_format($kat['total']) ?></small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small class="text-muted">Belum ada pengeluaran</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Riwayat Transaksi
                        </h5>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addTransactionModal">
                            <i class="fas fa-plus me-1"></i>Tambah
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="12%">
                                            <i class="fas fa-calendar me-2"></i>Tanggal
                                        </th>
                                        <th width="10%">
                                            <i class="fas fa-exchange-alt me-2"></i>Tipe
                                        </th>
                                        <th width="20%">
                                            <i class="fas fa-tag me-2"></i>Kategori
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-money-bill-wave me-2"></i>Jumlah
                                        </th>
                                        <th width="35%">
                                            <i class="fas fa-sticky-note me-2"></i>Catatan
                                        </th>
                                        <th width="8%">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $data = mysqli_query($conn, "SELECT * FROM keuangan ORDER BY created_at DESC");
                                    if (mysqli_num_rows($data) > 0):
                                        while ($row = mysqli_fetch_assoc($data)):
                                            ?>
                                            <tr>
                                                <td>
                                                    <div><?= date('d/m/Y', strtotime($row['created_at'])) ?></div>
                                                    <small
                                                        class="text-muted"><?= date('H:i', strtotime($row['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?= $row['tipe'] == 'masuk' ? 'success' : 'danger' ?>">
                                                        <i
                                                            class="fas fa-arrow-<?= $row['tipe'] == 'masuk' ? 'up' : 'down' ?> me-1"></i>
                                                        <?= ucfirst($row['tipe']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= ucfirst($row['kategori']) ?></div>
                                                </td>
                                                <td>
                                                    <div
                                                        class="fw-bold text-<?= $row['tipe'] == 'masuk' ? 'success' : 'danger' ?>">
                                                        <?= $row['tipe'] == 'masuk' ? '+' : '-' ?>Rp
                                                        <?= number_format($row['jumlah']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?= $row['catatan'] ?></span>
                                                </td>
                                                <td>
                                                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <h5>Belum Ada Transaksi</h5>
                                                    <p>Silakan tambah transaksi keuangan terlebih dahulu</p>
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

    <!-- Add Transaction Modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Tambah Transaksi Keuangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-exchange-alt me-2"></i>Tipe Transaksi
                                </label>
                                <select name="tipe" class="form-select" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="masuk">💰 Pemasukan</option>
                                    <option value="keluar">💸 Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave me-2"></i>Jumlah (Rp)
                                </label>
                                <input type="number" name="jumlah" class="form-control" placeholder="0" required
                                    min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tag me-2"></i>Kategori
                            </label>
                            <input type="text" name="kategori" class="form-control"
                                placeholder="Contoh: Pembayaran qurban, Biaya operasional, dll" required>
                            <div class="form-text">
                                Contoh kategori: pembayaran qurban, biaya administrasi, biaya operasional, pembelian
                                peralatan, dll.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Catatan
                            </label>
                            <textarea name="catatan" class="form-control" rows="4"
                                placeholder="Catatan detail tentang transaksi ini..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                    </div>
                </form>
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