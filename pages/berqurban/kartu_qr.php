<?php
session_start();
include "../../config/koneksi.php";
require_once "../../assets/phpqrcode/qrlib.php";

$nik = $_SESSION['nik'];

// Cek distribusi
$data = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d 
    JOIN users u ON d.user_nik = u.nik WHERE d.user_nik = '$nik'");

$distribusi = mysqli_fetch_assoc($data);

if (!$distribusi) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kartu QR - Sistem Qurban</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <div class="qr-container">
            <div class="qr-card error-card">
                <div class="qr-header error-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Kartu Belum Tersedia</h2>
                </div>
                <div class="qr-content">
                    <div class="error-message">
                        <i class="fas fa-info-circle"></i>
                        <p>Belum ada pembagian daging untuk Anda. Silakan hubungi panitia untuk informasi lebih lanjut.</p>
                    </div>
                    <div class="qr-actions">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Lokasi simpan QR
$folder = "../../assets/qrcodes/";
if (!file_exists($folder)) mkdir($folder, 0777, true);

$filename = $folder . $distribusi['user_nik'] . ".png";
$isi_qr = $distribusi['token'];

// Generate QR
QRcode::png($isi_qr, $filename, QR_ECLEVEL_H, 8);

// Get current date
$current_date = date('d F Y');
$current_time = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR - Sistem Qurban</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="qr-container">
        <!-- Navigation -->
        <nav class="qr-nav no-print">
            <div class="nav-content">
                <div class="nav-left">
                    <a href="index.php" class="nav-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
                <div class="nav-right">
                    <button onclick="window.print()" class="btn btn-outline">
                        <i class="fas fa-print"></i>
                        Cetak Kartu
                    </button>
                    <button onclick="downloadQR()" class="btn btn-primary">
                        <i class="fas fa-download"></i>
                        Download QR
                    </button>
                </div>
            </div>
        </nav>

        <!-- QR Card -->
        <div class="qr-card">
            <!-- Card Header -->
            <div class="qr-header">
                <div class="header-logo">
                    <i class="fas fa-mosque"></i>
                </div>
                <div class="header-content">
                    <h1>Kartu Pengambilan</h1>
                    <h2>Daging Qurban 1446H</h2>
                    <p class="header-subtitle">Masjid Al-Ikhlas</p>
                </div>
                <div class="header-date">
                    <div class="date-info">
                        <span class="date"><?= $current_date ?></span>
                        <span class="time"><?= $current_time ?> WIB</span>
                    </div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="qr-content">
                <div class="qr-info-section">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Nama Lengkap</span>
                                <span class="info-value"><?= htmlspecialchars($distribusi['name']) ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">NIK</span>
                                <span class="info-value"><?= htmlspecialchars($distribusi['user_nik']) ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Status</span>
                                <span class="info-value role-badge role-<?= strtolower($distribusi['role']) ?>">
                                    <?= ucfirst($distribusi['role']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="info-item highlight">
                            <div class="info-icon">
                                <i class="fas fa-weight"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Jumlah Daging</span>
                                <span class="info-value weight"><?= number_format($distribusi['jumlah_daging']) ?> gram</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="qr-code-section">
                    <div class="qr-code-container">
                        <div class="qr-code-wrapper">
                            <img src="<?= $filename ?>" alt="QR Code" class="qr-code-image">
                        </div>
                        <div class="qr-code-info">
                            <p class="qr-token">Token: <code><?= htmlspecialchars($distribusi['token']) ?></code></p>
                            <div class="status-indicator">
                                <span class="status-badge status-<?= strtolower($distribusi['status_ambil']) ?>">
                                    <i class="fas fa-<?= $distribusi['status_ambil'] == 'belum' ? 'clock' : 'check-circle' ?>"></i>
                                    <?= ucfirst($distribusi['status_ambil']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="qr-instructions">
                    <div class="instruction-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Petunjuk Pengambilan</h3>
                    </div>
                    <div class="instruction-list">
                        <div class="instruction-item">
                            <span class="step-number">1</span>
                            <span class="step-text">Tunjukkan kartu ini kepada petugas panitia</span>
                        </div>
                        <div class="instruction-item">
                            <span class="step-number">2</span>
                            <span class="step-text">Petugas akan memindai QR Code untuk verifikasi</span>
                        </div>
                        <div class="instruction-item">
                            <span class="step-number">3</span>
                            <span class="step-text">Siapkan wadah/kantong untuk mengambil daging</span>
                        </div>
                        <div class="instruction-item">
                            <span class="step-number">4</span>
                            <span class="step-text">Daging akan ditimbang sesuai dengan alokasi Anda</span>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="qr-notes">
                    <div class="note-item important">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Kartu ini hanya berlaku untuk satu kali pengambilan</span>
                    </div>
                    <div class="note-item">
                        <i class="fas fa-clock"></i>
                        <span>Jam pengambilan: 08:00 - 16:00 WIB</span>
                    </div>
                    <div class="note-item">
                        <i class="fas fa-phone"></i>
                        <span>Hubungi panitia: 0812-3456-7890 untuk informasi</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="qr-footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <p class="footer-text">Sistem Manajemen Qurban Digital</p>
                        <p class="footer-subtitle">Masjid Al-Ikhlas - Tahun 1446H</p>
                    </div>
                    <div class="footer-right">
                        <div class="footer-qr-mini">
                            <i class="fas fa-qrcode"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons (No Print) -->
        <div class="qr-actions no-print">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i>
                Cetak Kartu
            </button>
            <button onclick="downloadQR()" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Download QR Code
            </button>
            <button class="btn btn-outline">
                <i class="fas fa-home"></i>
                <a href="index.php">Kembali ke Dashboard</a>
            </button>
        </div>
    </div>

    <script>
        function downloadQR() {
            const link = document.createElement('a');
            link.href = '<?= $filename ?>';
            link.download = 'qr-code-<?= $distribusi['user_nik'] ?>.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Print optimization
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });
    </script>
</body>
</html>
