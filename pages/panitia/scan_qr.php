<?php
session_start();
if ($_SESSION['role'] != 'panitia') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Proses validasi token
$message = '';
$messageType = '';
$scanData = null;

if (isset($_POST['token'])) {
    $token = trim($_POST['token']);
    $cek = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d JOIN users u ON d.user_nik = u.nik WHERE d.token='$token'");
    $data = mysqli_fetch_assoc($cek);

    if ($data && $data['status_ambil'] == 'belum') {
        mysqli_query($conn, "UPDATE distribusi SET status_ambil='diambil', tanggal_ambil=NOW() WHERE token='$token'");
        $message = "Daging berhasil diberikan kepada <strong>{$data['name']}</strong>";
        $messageType = 'success';
        $scanData = $data;
    } elseif ($data && $data['status_ambil'] == 'diambil') {
        $message = "Daging untuk <strong>{$data['name']}</strong> sudah diambil sebelumnya!";
        $messageType = 'warning';
        $scanData = $data;
    } else {
        $message = "Token tidak valid atau belum terdaftar dalam sistem!";
        $messageType = 'error';
    }
}

// Get recent scans for activity feed
// $recentScans = mysqli_query($conn, "
//     SELECT d.*, u.name, u.role 
//     FROM distribusi d 
//     JOIN users u ON d.user_nik = u.nik 
//     WHERE d.status_ambil = 'diambil' 
//     ORDER BY d.tanggal_ambil DESC 
//     LIMIT 5
// ");

// Get statistics
$totalDistribusi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi"))['total'];
$sudahDiambil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM distribusi WHERE status_ambil='diambil'"))['total'];
$belumDiambil = $totalDistribusi - $sudahDiambil;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="scanner-body">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-qrcode text-primary me-2"></i>
                <span class="fw-bold">QR Scanner</span>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <span><?= $_SESSION['nama'] ?></span>
                            <small class="role-badge role-panitia d-block">Panitia</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../panitia/index.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="scanner-container">
        <!-- Page Header -->
        <div class="scanner-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-content">
                            <h1 class="header-title">
                                <i class="fas fa-qrcode me-3"></i>
                                Scanner QR Code
                            </h1>
                            <p class="header-subtitle">Scan QR code untuk verifikasi pengambilan daging qurban</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="header-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?= $sudahDiambil ?></span>
                                <span class="stat-label">Sudah Diambil</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?= $belumDiambil ?></span>
                                <span class="stat-label">Belum Diambil</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <!-- Scanner Section -->
                <div class="col-lg-8">
                    <div class="scanner-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-keyboard me-2"></i>
                                Verifikasi Token QR Code
                            </h5>
                            <div class="scanner-info">
                                <span class="badge bg-primary">Manual Input</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Manual Input Section -->
                            <div class="scanner-input-section">
                                <div class="input-header">
                                    <h5 class="input-title">
                                        <i class="fas fa-keyboard me-2"></i>
                                        Masukkan Token QR Code
                                    </h5>
                                    <p class="input-subtitle">Ketik atau scan token QR code untuk verifikasi pengambilan daging</p>
                                </div>
                                
                                <form method="POST" class="token-form" id="tokenForm">
                                    <div class="token-input-group">
                                        <div class="input-icon">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <input type="text" 
                                               class="form-control token-input" 
                                               name="token" 
                                               id="tokenInput"
                                               placeholder="Masukkan token QR code..." 
                                               required
                                               autocomplete="off"
                                               spellcheck="false">
                                        <button type="submit" class="btn btn-primary verify-btn">
                                            <i class="fas fa-search me-2"></i>
                                            Verifikasi Token
                                        </button>
                                    </div>
                                    <div class="input-help">
                                        <div class="help-item">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Token terdiri dari kombinasi huruf dan angka (contoh: ABC123DEF456)
                                        </div>
                                        <div class="help-item">
                                            <i class="fas fa-keyboard me-2"></i>
                                            Tekan Enter atau klik tombol Verifikasi untuk memproses
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Result -->
                    <?php if ($message): ?>
                    <div class="scan-result-card fade-in-up">
                        <div class="card scan-result <?= $messageType ?>">
                            <div class="card-body">
                                <div class="result-content">
                                    <div class="result-icon">
                                        <?php if ($messageType == 'success'): ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php elseif ($messageType == 'warning'): ?>
                                            <i class="fas fa-exclamation-triangle"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="result-details">
                                        <h5 class="result-title">
                                            <?php if ($messageType == 'success'): ?>
                                                Berhasil Diverifikasi
                                            <?php elseif ($messageType == 'warning'): ?>
                                                Sudah Diambil
                                            <?php else: ?>
                                                Token Tidak Valid
                                            <?php endif; ?>
                                        </h5>
                                        <p class="result-message"><?= $message ?></p>
                                        
                                        <?php if ($scanData): ?>
                                        <div class="scan-details">
                                            <div class="detail-row">
                                                <span class="detail-label">NIK:</span>
                                                <span class="detail-value"><?= $scanData['user_nik'] ?></span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Berat Daging:</span>
                                                <span class="detail-value"><?= $scanData['berat_daging'] ?> kg</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Token:</span>
                                                <span class="detail-value"><code><?= $scanData['token'] ?></code></span>
                                            </div>
                                            <?php if ($scanData['tanggal_ambil']): ?>
                                            <div class="detail-row">
                                                <span class="detail-label">Tanggal Ambil:</span>
                                                <span class="detail-value"><?= date('d/m/Y H:i', strtotime($scanData['tanggal_ambil'])) ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="result-actions">
                                    <button type="button" class="btn btn-outline-primary" onclick="location.reload()">
                                        <i class="fas fa-redo me-1"></i>
                                        Scan Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Activity Sidebar -->
                <div class="col-lg-4">
                    <!-- Statistics Card -->
                    <div class="stats-card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Statistik Hari Ini
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <div class="stat-box success">
                                    <div class="stat-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number"><?= $sudahDiambil ?></span>
                                        <span class="stat-label">Sudah Diambil</span>
                                    </div>
                                </div>
                                
                                <div class="stat-box warning">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number"><?= $belumDiambil ?></span>
                                        <span class="stat-label">Belum Diambil</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress-section">
                                <div class="progress-info">
                                    <span>Progress Distribusi</span>
                                    <span><?= $totalDistribusi > 0 ? round(($sudahDiambil / $totalDistribusi) * 100) : 0 ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= $totalDistribusi > 0 ? ($sudahDiambil / $totalDistribusi) * 100 : 0 ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions-card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Aksi Cepat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="../panitia/index.php" class="quick-action-btn">
                                    <i class="fas fa-home"></i>
                                    <span>Dashboard</span>
                                </a>
                                <a href="../admin/distribusi_list.php" class="quick-action-btn">
                                    <i class="fas fa-list"></i>
                                    <span>Daftar Distribusi</span>
                                </a>
                                <button type="button" class="quick-action-btn" onclick="location.reload()">
                                    <i class="fas fa-sync"></i>
                                    <span>Refresh</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Focus on token input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('tokenInput').focus();
        });

        // Auto-submit on Enter key
        document.getElementById('tokenInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('tokenForm').submit();
            }
        });

        // Format token input (uppercase and remove spaces)
        document.getElementById('tokenInput').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/\s/g, '');
            e.target.value = value;
        });

        // Auto-dismiss alerts after 8 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.scan-result-card');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 8000);

        // Add loading state to verify button
        document.getElementById('tokenForm').addEventListener('submit', function() {
            const btn = document.querySelector('.verify-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memverifikasi...';
            btn.disabled = true;
            
            // Re-enable after 3 seconds (fallback)
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html>
