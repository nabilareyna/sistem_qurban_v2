<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

include "../../config/koneksi.php";

// Handle tambah hewan
if (isset($_POST['tambah'])) {
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'] ?? '';

    $query = mysqli_query($conn, "INSERT INTO hewans (jenis, harga, deskripsi, created_at) VALUES ('$jenis', '$harga', '$deskripsi', NOW())");
    if ($query) {
        $success = "Hewan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan hewan: " . mysqli_error($conn);
    }
}

// Handle edit hewan
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'] ?? '';

    $query = mysqli_query($conn, "UPDATE hewans SET jenis='$jenis', harga='$harga', deskripsi='$deskripsi' WHERE id='$id'");
    if ($query) {
        $success = "Data hewan berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui data hewan: " . mysqli_error($conn);
    }
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Check if animal is being used in qurban
    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM qurbans WHERE hewan_id='$id'");
    $count = mysqli_fetch_assoc($check)['count'];
    
    if ($count > 0) {
        $error = "Tidak dapat menghapus hewan yang sudah digunakan dalam qurban!";
    } else {
        mysqli_query($conn, "DELETE FROM hewans WHERE id=$id");
        $success = "Hewan berhasil dihapus!";
    }
}

// Get statistics
$stats = [];
$stats['total_hewans'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hewans"))['total'];
$stats['total_sapi'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hewans WHERE jenis='sapi'"))['total'];
$stats['total_kambing'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hewans WHERE jenis='kambing'"))['total'];
$stats['hewan_terpakai'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT hewan_id) as total FROM qurbans"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Hewan Qurban - Sistem Qurban</title>
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
                        <a class="nav-link active" href="data_hewan.php">
                            <i class="fas fa-cow me-1"></i>Kelola Hewan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="qurban_list.php">
                            <i class="fas fa-list me-1"></i>Data Qurban
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
                                    <i class="fas fa-cow me-2"></i>Kelola Hewan Qurban
                                </h2>
                                <p class="text-white-50 mb-0">Manajemen data hewan untuk ibadah qurban</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addAnimalModal">
                                    <i class="fas fa-plus me-2"></i>Tambah Hewan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-paw fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $stats['total_hewans'] ?></div>
                        <div class="stat-label">Total Hewan</div>
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
                        <div class="stat-number text-warning"><?= $stats['total_sapi'] ?></div>
                        <div class="stat-label">Sapi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-sheep fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= $stats['total_kambing'] ?></div>
                        <div class="stat-label">Kambing</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-info"><?= $stats['hewan_terpakai'] ?></div>
                        <div class="stat-label">Hewan Terpakai</div>
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

        <!-- Animals Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Hewan Qurban
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAnimalModal">
                                <i class="fas fa-plus me-1"></i>Tambah Hewan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">
                                            <i class="fas fa-paw me-2"></i>Jenis
                                        </th>
                                        <th width="20%">
                                            <i class="fas fa-money-bill-wave me-2"></i>Harga
                                        </th>
                                        <th width="25%">
                                            <i class="fas fa-info-circle me-2"></i>Deskripsi
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-calendar me-2"></i>Ditambahkan
                                        </th>
                                        <th width="10%">
                                            <i class="fas fa-users me-2"></i>Digunakan
                                        </th>
                                        <th width="10%">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $result = mysqli_query($conn, "SELECT h.*, 
                                        (SELECT COUNT(*) FROM qurbans WHERE hewan_id = h.id) as usage_count 
                                        FROM hewans h ORDER BY h.created_at DESC");
                                    
                                    if (mysqli_num_rows($result) > 0):
                                        while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-<?= $row['jenis'] == 'sapi' ? 'warning' : 'success' ?> bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="fas fa-<?= $row['jenis'] == 'sapi' ? 'cow' : 'sheep' ?> text-<?= $row['jenis'] == 'sapi' ? 'warning' : 'success' ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?= ucfirst($row['jenis']) ?></div>
                                                        <?php if ($row['jenis'] == 'sapi'): ?>
                                                            <small class="text-muted">Patungan 1-7 orang</small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Individual</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">Rp <?= number_format($row['harga']) ?></div>
                                                <?php if ($row['jenis'] == 'sapi'): ?>
                                                    <small class="text-muted">~Rp <?= number_format($row['harga'] / 7) ?>/orang</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['deskripsi'])): ?>
                                                    <span class="text-muted"><?= $row['deskripsi'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">Tidak ada deskripsi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div><?= date('d/m/Y', strtotime($row['created_at'])) ?></div>
                                                <small class="text-muted"><?= date('H:i', strtotime($row['created_at'])) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($row['usage_count'] > 0): ?>
                                                    <span class="badge bg-success"><?= $row['usage_count'] ?> kali</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Belum</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-warning" 
                                                            onclick="editAnimal(<?= htmlspecialchars(json_encode($row)) ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#editAnimalModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($row['usage_count'] == 0): ?>
                                                        <a href="?hapus=<?= $row['id'] ?>" 
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirm('Yakin ingin menghapus hewan <?= $row['jenis'] ?> ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat dihapus karena sudah digunakan">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <h5>Belum Ada Data Hewan</h5>
                                                    <p>Silakan tambah hewan qurban terlebih dahulu</p>
                                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnimalModal">
                                                        <i class="fas fa-plus me-2"></i>Tambah Hewan Pertama
                                                    </button>
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

    <!-- Add Animal Modal -->
    <div class="modal fade" id="addAnimalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Tambah Hewan Qurban
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis" class="form-label">
                                <i class="fas fa-paw me-2"></i>Jenis Hewan
                            </label>
                            <select name="jenis" id="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis Hewan --</option>
                                <option value="sapi">🐄 Sapi (Patungan 1-7 orang)</option>
                                <option value="kambing">🐐 Kambing (Individual)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Harga (Rp)
                            </label>
                            <input type="number" name="harga" id="harga" class="form-control" 
                                   placeholder="Masukkan harga hewan" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-info-circle me-2"></i>Deskripsi (Opsional)
                            </label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" 
                                      placeholder="Deskripsi tambahan tentang hewan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Hewan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Animal Modal -->
    <div class="modal fade" id="editAnimalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Hewan Qurban
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_jenis" class="form-label">
                                <i class="fas fa-paw me-2"></i>Jenis Hewan
                            </label>
                            <select name="jenis" id="edit_jenis" class="form-select" required>
                                <option value="sapi">🐄 Sapi (Patungan 1-7 orang)</option>
                                <option value="kambing">🐐 Kambing (Individual)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_harga" class="form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Harga (Rp)
                            </label>
                            <input type="number" name="harga" id="edit_harga" class="form-control" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">
                                <i class="fas fa-info-circle me-2"></i>Deskripsi (Opsional)
                            </label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" name="edit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update Hewan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editAnimal(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_jenis').value = data.jenis;
            document.getElementById('edit_harga').value = data.harga;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        }

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
