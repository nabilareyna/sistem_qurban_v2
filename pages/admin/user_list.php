<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

// Get statistics
$stats = [];
$stats['total_users'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$stats['admin'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];
$stats['panitia'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='panitia'"))['total'];
$stats['berqurban'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='berqurban'"))['total'];
$stats['warga'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='warga'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Sistem Qurban</title>
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
                        <a class="nav-link active" href="user_list.php">
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
                                    <i class="fas fa-users me-2"></i>Kelola User
                                </h2>
                                <p class="text-white-50 mb-0">Manajemen pengguna sistem qurban</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="../../register.php" class="btn btn-light">
                                    <i class="fas fa-user-plus me-2"></i>Tambah User
                                </a>
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
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $stats['total_users'] ?></div>
                        <div class="stat-label">Total User</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-shield fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="stat-number text-danger"><?= $stats['admin'] ?></div>
                        <div class="stat-label">Admin</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-tie fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="stat-number text-warning"><?= $stats['panitia'] ?></div>
                        <div class="stat-label">Panitia</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-check fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="stat-number text-success"><?= $stats['berqurban'] ?></div>
                        <div class="stat-label">Berqurban</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="stat-number text-info"><?= $stats['warga'] ?></div>
                        <div class="stat-label">Warga</div>
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
                            <?= $stats['total_users'] > 0 ? round(($stats['berqurban'] / $stats['total_users']) * 100) : 0 ?>%
                        </div>
                        <div class="stat-label">Partisipasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Daftar User
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filter Role
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?role=all">Semua Role</a></li>
                                    <li><a class="dropdown-item" href="?role=admin">Admin</a></li>
                                    <li><a class="dropdown-item" href="?role=panitia">Panitia</a></li>
                                    <li><a class="dropdown-item" href="?role=berqurban">Berqurban</a></li>
                                    <li><a class="dropdown-item" href="?role=warga">Warga</a></li>
                                </ul>
                            </div>
                            <a href="../../register.php" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus me-1"></i>Tambah User
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="12%">
                                            <i class="fas fa-id-card me-2"></i>NIK
                                        </th>
                                        <th width="25%">
                                            <i class="fas fa-user me-2"></i>Nama
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-at me-2"></i>Username
                                        </th>
                                        <th width="12%">
                                            <i class="fas fa-user-tag me-2"></i>Role
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-phone me-2"></i>No HP
                                        </th>
                                        <th width="15%">
                                            <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                        </th>
                                        <th width="6%">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $role_filter = $_GET['role'] ?? 'all';
                                    $where_clause = "";
                                    if ($role_filter != 'all') {
                                        $where_clause = "WHERE role = '$role_filter'";
                                    }

                                    $users = mysqli_query($conn, "SELECT * FROM users $where_clause ORDER BY 
                                        CASE role 
                                            WHEN 'admin' THEN 1 
                                            WHEN 'panitia' THEN 2 
                                            WHEN 'berqurban' THEN 3 
                                            WHEN 'warga' THEN 4 
                                        END, name ASC");

                                    if (mysqli_num_rows($users) > 0):
                                        while ($row = mysqli_fetch_assoc($users)):
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?= $row['nik'] ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-<?=
                                                            $row['role'] == 'admin' ? 'danger' :
                                                            ($row['role'] == 'panitia' ? 'warning' :
                                                                ($row['role'] == 'berqurban' ? 'success' : 'info'))
                                                            ?> bg-opacity-10 rounded-circle p-2 me-3">
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
                                                            <small class="text-muted">ID: <?= $row['nik'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= $row['username'] ?></div>
                                                </td>
                                                <td>
                                                    <span class="role-badge role-<?= $row['role'] ?>">
                                                        <i class="fas fa-<?=
                                                            $row['role'] == 'admin' ? 'user-shield' :
                                                            ($row['role'] == 'panitia' ? 'user-tie' :
                                                                ($row['role'] == 'berqurban' ? 'user-check' : 'user'))
                                                            ?> me-1"></i>
                                                        <?= strtoupper($row['role']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['no_hp'])): ?>
                                                        <div class="fw-semibold"><?= $row['no_hp'] ?></div>
                                                    <?php else: ?>
                                                        <span class="text-muted fst-italic">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['alamat'])): ?>
                                                        <div class="text-truncate" style="max-width: 150px;"
                                                            title="<?= $row['alamat'] ?>">
                                                            <?= $row['alamat'] ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted fst-italic">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="edit_user.php?nik=<?= $row['nik'] ?>"
                                                            class="btn btn-sm btn-warning" title="Edit User">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($row['nik'] != $_SESSION['nik']): ?>
                                                            <a href="delete_user.php?nik=<?= $row['nik'] ?>"
                                                                class="btn btn-sm btn-danger" title="Hapus User"
                                                                onclick="return confirm('Yakin ingin hapus user <?= $row['name'] ?>?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-secondary" disabled
                                                                title="Tidak dapat menghapus diri sendiri">
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
                                                    <h5>Tidak Ada User</h5>
                                                    <p>Tidak ada user dengan filter yang dipilih</p>
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
</body>

</html>