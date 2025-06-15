<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";
require_once "../../assets/phpqrcode/qrlib.php";

if (!isset($_GET['nik'])) {
    echo "NIK tidak ditemukan di URL.";
    exit;
}

$nik = $_GET['nik'];
$check = mysqli_query($conn, "SELECT d.*, u.name, u.role FROM distribusi d JOIN users u ON d.user_nik = u.nik WHERE d.user_nik='$nik'");
$data = mysqli_fetch_assoc($check);

if (!$data) {
    echo "<p style='color:red;'>Distribusi tidak ditemukan untuk NIK ini.</p>";
    exit;
}

// Buat/generate QR
$folder = "../../assets/qrcodes/";
if (!file_exists($folder))
    mkdir($folder);

$filename = $folder . $data['user_nik'] . ".png";
QRcode::png($data['token'], $filename, QR_ECLEVEL_H, 6);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR Distribusi - Admin View</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-qr-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-card-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            position: relative;
        }

        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .admin-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .admin-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .admin-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .qr-card-content {
            padding: 30px;
        }

        .user-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
        }

        .info-item.role {
            border-left-color: #10b981;
        }

        .info-item.weight {
            border-left-color: #f59e0b;
            grid-column: span 2;
            text-align: center;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .info-item.weight .info-value {
            font-size: 24px;
            color: #d97706;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 16px;
            border: 2px dashed #cbd5e1;
        }

        .qr-code-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .qr-code-container img {
            display: block;
            border-radius: 8px;
        }

        .token-display {
            background: #1e293b;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 1px;
            margin-top: 15px;
            word-break: break-all;
        }

        .admin-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .admin-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .status-indicator {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .generation-info {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border-radius: 8px;
            border: 1px solid #bae6fd;
        }

        .generation-info small {
            color: #0369a1;
            font-weight: 500;
        }

        @media print {
            .admin-qr-container {
                background: white;
                padding: 0;
            }
            
            .admin-actions,
            .admin-badge {
                display: none;
            }
            
            .qr-card-wrapper {
                box-shadow: none;
                border: 2px solid #000;
            }
        }

        @media (max-width: 768px) {
            .admin-qr-container {
                padding: 10px;
            }
            
            .user-info-grid {
                grid-template-columns: 1fr;
            }
            
            .info-item.weight {
                grid-column: span 1;
            }
            
            .admin-actions {
                flex-direction: column;
            }
            
            .admin-btn {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="admin-qr-container">
        <div class="qr-card-wrapper">
            <div class="status-indicator">
                <i class="fas fa-check-circle"></i> QR Generated
            </div>
            
            <div class="admin-header">
                <div class="admin-badge">
                    <i class="fas fa-user-shield"></i> ADMIN VIEW
                </div>
                <h2>
                    <i class="fas fa-qrcode"></i>
                    Kartu QR Distribusi
                </h2>
                <p>Sistem Manajemen Qurban Digital</p>
            </div>

            <div class="qr-card-content">
                <div class="user-info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </div>
                        <div class="info-value"><?= htmlspecialchars($data['name']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i> NIK
                        </div>
                        <div class="info-value"><?= htmlspecialchars($data['user_nik']) ?></div>
                    </div>
                    
                    <div class="info-item role">
                        <div class="info-label">
                            <i class="fas fa-user-tag"></i> Role
                        </div>
                        <div class="info-value">
                            <?php
                            $roleIcons = [
                                'admin' => 'fas fa-user-shield',
                                'warga' => 'fas fa-users',
                                'panitia' => 'fas fa-user-tie',
                                'berqurban' => 'fas fa-hand-holding-heart'
                            ];
                            $roleIcon = $roleIcons[$data['role']] ?? 'fas fa-user';
                            ?>
                            <i class="<?= $roleIcon ?>"></i>
                            <?= ucfirst(htmlspecialchars($data['role'])) ?>
                        </div>
                    </div>
                    
                    <div class="info-item weight">
                        <div class="info-label">
                            <i class="fas fa-weight"></i> Alokasi Daging
                        </div>
                        <div class="info-value"><?= number_format($data['jumlah_daging']) ?> gram</div>
                    </div>
                </div>

                <div class="qr-section">
                    <div class="qr-code-container">
                        <img src="<?= $filename ?>" width="200" height="200" alt="QR Code">
                    </div>
                    
                    <div class="token-display">
                        <i class="fas fa-key"></i> <?= htmlspecialchars($data['token']) ?>
                    </div>
                </div>

                <div class="generation-info">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        QR Code berhasil di-generate pada <?= date('d/m/Y H:i:s') ?>
                    </small>
                </div>

                <div class="admin-actions">
                    <button onclick="window.print()" class="admin-btn btn-primary">
                        <i class="fas fa-print"></i> Print Kartu
                    </button>
                    
                    <a href="<?= $filename ?>" download="qr_<?= $data['user_nik'] ?>.png" class="admin-btn btn-success">
                        <i class="fas fa-download"></i> Download QR
                    </a>
                    
                    <a href="distribusi_list.php" class="admin-btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Print optimization
        window.addEventListener('beforeprint', function() {
            document.body.style.background = 'white';
        });

        window.addEventListener('afterprint', function() {
            document.body.style.background = '';
        });

        // Auto-focus for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add subtle animation
            const card = document.querySelector('.qr-card-wrapper');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>

</html>
