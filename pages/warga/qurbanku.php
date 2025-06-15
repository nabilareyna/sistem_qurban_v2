<?php
session_start();
if ($_SESSION['role'] != 'warga') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_SESSION['nik'];

// Get user information
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'");
$user = mysqli_fetch_assoc($user_query);

if (isset($_POST['daftar'])) {
    $hewan_id = $_POST['hewan_id'];
    $jumlah = $_POST['jumlah'];
    $cek = mysqli_query($conn, "SELECT * FROM qurbans WHERE user_nik='$nik'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Anda sudah terdaftar untuk qurban.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO qurbans (user_nik, hewan_id, jumlah, status_bayar, created_at) VALUES ('$nik', '$hewan_id', '$jumlah', 'belum', NOW())");
        if ($insert) {
            $success = "Pendaftaran berhasil! Silakan lakukan pembayaran.";
        } else {
            $error = "Gagal menyimpan data. Silakan coba lagi.";
        }
    }
}

// Ambil data qurban user ini
$dataqurban = mysqli_query($conn, "SELECT q.*, h.jenis, h.harga FROM qurbans q JOIN hewans h ON q.hewan_id = h.id WHERE user_nik='$nik'");
$q = mysqli_fetch_assoc($dataqurban);

// Get available animals
$hewans = mysqli_query($conn, "SELECT * FROM hewans ORDER BY jenis, harga");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qurban Saya - Sistem Manajemen Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-mosque me-2"></i>
                Qurban Management
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <small class="text-muted d-block">Warga</small>
                            <span><?= htmlspecialchars($user['name']) ?></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">
                                <i class="fas fa-user-check me-3"></i>
                                Qurban Saya
                            </h1>
                            <p class="page-subtitle">Kelola pendaftaran dan status qurban Anda</p>
                        </div>
                        <div class="page-actions">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($error)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$q): ?>
        <!-- Registration Form -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card qurban-registration-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Daftar Qurban
                        </h5>
                        <p class="card-subtitle">Pilih jenis hewan dan jumlah patungan untuk qurban Anda</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="registrationForm">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="hewan_id" class="form-label">
                                        <i class="fas fa-paw me-2"></i>Jenis Hewan
                                    </label>
                                    <select name="hewan_id" id="hewan_id" class="form-select" required>
                                        <option value="">-- Pilih Jenis Hewan --</option>
                                        <?php while ($h = mysqli_fetch_assoc($hewans)): ?>
                                        <option value="<?= $h['id'] ?>" 
                                                data-jenis="<?= $h['jenis'] ?>" 
                                                data-harga="<?= $h['harga'] ?>"
                                                <?= (isset($_POST['hewan_id']) && $_POST['hewan_id'] == $h['id']) ? 'selected' : '' ?>>
                                            <?= ucfirst($h['jenis']) ?> - Rp <?= number_format($h['harga'], 0, ',', '.') ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4" id="jumlahContainer" style="display: none;">
                                    <label for="jumlah" class="form-label">
                                        <i class="fas fa-users me-2"></i>Jumlah Patungan
                                    </label>
                                    <select name="jumlah" id="jumlah" class="form-select">
                                        <option value="">-- Pilih Jumlah --</option>
                                        <?php for($i = 1; $i <= 7; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> orang</option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Untuk sapi, maksimal 7 orang patungan
                                    </div>
                                </div>
                            </div>

                            <!-- Price Preview -->
                            <div id="pricePreview" class="price-preview-card" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="price-item">
                                            <span class="price-label">Harga Hewan:</span>
                                            <span class="price-value" id="hargaHewan">-</span>
                                        </div>
                                        <div class="price-item">
                                            <span class="price-label">Jumlah Patungan:</span>
                                            <span class="price-value" id="jumlahPatungan">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="price-item">
                                            <span class="price-label">Biaya Admin:</span>
                                            <span class="price-value">Rp 100.000</span>
                                        </div>
                                        <div class="price-item total-price">
                                            <span class="price-label">Total Bayar:</span>
                                            <span class="price-value" id="totalBayar">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="daftar" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-check me-2"></i>
                                    Daftar Qurban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Qurban Status -->
        <?php 
        $biaya_admin = 100000;
        $harga_per_orang = $q['jenis'] == 'sapi' ? $q['harga'] / 7 : $q['harga'];
        $total = ($harga_per_orang * $q['jumlah']) + $biaya_admin;
        ?>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Status Card -->
                <div class="card qurban-status-card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clipboard-check me-2"></i>
                                    Status Qurban Anda
                                </h5>
                                <p class="card-subtitle">Informasi lengkap pendaftaran qurban</p>
                            </div>
                            <div class="status-badge status-<?= $q['status_bayar'] ?>">
                                <i class="fas fa-<?= $q['status_bayar'] == 'sudah' ? 'check-circle' : 'clock' ?> me-1"></i>
                                <?= strtoupper($q['status_bayar']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="qurban-detail">
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-paw"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Jenis Hewan</span>
                                            <span class="detail-value"><?= ucfirst($q['jenis']) ?></span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Jumlah Patungan</span>
                                            <span class="detail-value"><?= $q['jumlah'] ?> orang</span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Tanggal Daftar</span>
                                            <span class="detail-value"><?= date('d/m/Y H:i', strtotime($q['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-summary">
                                    <h6 class="summary-title">
                                        <i class="fas fa-calculator me-2"></i>
                                        Rincian Pembayaran
                                    </h6>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Harga Hewan</span>
                                        <span class="summary-value">Rp <?= number_format($q['harga'], 0, ',', '.') ?></span>
                                    </div>
                                    
                                    <?php if ($q['jenis'] == 'sapi'): ?>
                                    <div class="summary-item">
                                        <span class="summary-label">Harga per Orang (÷7)</span>
                                        <span class="summary-value">Rp <?= number_format($harga_per_orang, 0, ',', '.') ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Jumlah Patungan (×<?= $q['jumlah'] ?>)</span>
                                        <span class="summary-value">Rp <?= number_format($harga_per_orang * $q['jumlah'], 0, ',', '.') ?></span>
                                    </div>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Biaya Admin</span>
                                        <span class="summary-value">Rp <?= number_format($biaya_admin, 0, ',', '.') ?></span>
                                    </div>
                                    
                                    <div class="summary-divider"></div>
                                    
                                    <div class="summary-item total">
                                        <span class="summary-label">Total Bayar</span>
                                        <span class="summary-value">Rp <?= number_format($total, 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($q['status_bayar'] == 'belum'): ?>
                        <div class="payment-notice">
                            <div class="notice-content">
                                <i class="fas fa-info-circle notice-icon"></i>
                                <div>
                                    <h6 class="notice-title">Menunggu Pembayaran</h6>
                                    <p class="notice-text">Silakan lakukan pembayaran sesuai dengan total yang tertera. Hubungi panitia untuk konfirmasi pembayaran.</p>
                                </div>
                            </div>
                            <div class="notice-actions">
                                <button class="btn btn-success" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Cetak Bukti
                                </button>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="payment-success">
                            <div class="success-content">
                                <i class="fas fa-check-circle success-icon"></i>
                                <div>
                                    <h6 class="success-title">Pembayaran Berhasil</h6>
                                    <p class="success-text">Terima kasih! Pembayaran Anda telah dikonfirmasi. Anda akan mendapatkan kartu QR untuk pengambilan daging.</p>
                                </div>
                            </div>
                            <div class="success-actions">
                                <a href="kartu_qr.php" class="btn btn-primary">
                                    <i class="fas fa-qrcode me-2"></i>Lihat Kartu QR
                                </a>
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Cetak Bukti
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hewanSelect = document.getElementById('hewan_id');
            const jumlahContainer = document.getElementById('jumlahContainer');
            const jumlahSelect = document.getElementById('jumlah');
            const pricePreview = document.getElementById('pricePreview');
            const submitBtn = document.getElementById('submitBtn');

            hewanSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const jenis = selectedOption.dataset.jenis;
                const harga = parseInt(selectedOption.dataset.harga) || 0;

                if (jenis === 'sapi') {
                    jumlahContainer.style.display = 'block';
                    jumlahSelect.required = true;
                } else if (jenis === 'kambing') {
                    jumlahContainer.style.display = 'none';
                    jumlahSelect.required = false;
                    jumlahSelect.value = '1';
                    updatePricePreview(harga, 1);
                } else {
                    jumlahContainer.style.display = 'none';
                    jumlahSelect.required = false;
                    pricePreview.style.display = 'none';
                    submitBtn.disabled = true;
                }
            });

            jumlahSelect.addEventListener('change', function() {
                const selectedHewan = hewanSelect.options[hewanSelect.selectedIndex];
                const harga = parseInt(selectedHewan.dataset.harga) || 0;
                const jumlah = parseInt(this.value) || 0;
                
                if (harga && jumlah) {
                    updatePricePreview(harga, jumlah);
                }
            });

            function updatePricePreview(harga, jumlah) {
                const selectedOption = hewanSelect.options[hewanSelect.selectedIndex];
                const jenis = selectedOption.dataset.jenis;
                const biayaAdmin = 100000;
                
                let hargaPerOrang = jenis === 'sapi' ? harga / 7 : harga;
                let totalHewan = hargaPerOrang * jumlah;
                let totalBayar = totalHewan + biayaAdmin;

                document.getElementById('hargaHewan').textContent = 'Rp ' + harga.toLocaleString('id-ID');
                document.getElementById('jumlahPatungan').textContent = jumlah + ' orang';
                document.getElementById('totalBayar').textContent = 'Rp ' + totalBayar.toLocaleString('id-ID');

                pricePreview.style.display = 'block';
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
