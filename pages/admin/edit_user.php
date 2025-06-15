<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}
include "../../config/koneksi.php";

$nik = $_GET['nik'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    header("Location: user_list.php");
    exit;
}

$success_message = '';
$error_message = '';

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    // Check if username already exists (excluding current user)
    $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND nik!='$nik'");
    if (mysqli_num_rows($check_username) > 0) {
        $error_message = "Username sudah digunakan oleh user lain!";
    } else {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = "UPDATE users SET name='$name', username='$username', password='$hashed', role='$role', alamat='$alamat', no_hp='$no_hp' WHERE nik='$nik'";
        } else {
            $update = "UPDATE users SET name='$name', username='$username', role='$role', alamat='$alamat', no_hp='$no_hp' WHERE nik='$nik'";
        }

        if (mysqli_query($conn, $update)) {
            $success_message = "Data user berhasil diperbarui!";
            // Refresh user data
            $query = mysqli_query($conn, "SELECT * FROM users WHERE nik='$nik'");
            $user = mysqli_fetch_assoc($query);
        } else {
            $error_message = "Update gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Qurban Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="../admin/index.php">
                <i class="fas fa-mosque me-2"></i>
                Qurban Management
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="user-info">
                            <small class="text-muted d-block">Admin</small>
                            <span><?= $_SESSION['nama'] ?></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../admin/index.php"><i
                                    class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../../logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header mb-4 fade-in-up">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-edit me-3"></i>
                        Edit User
                    </h1>
                    <p class="page-subtitle">Perbarui informasi pengguna sistem</p>
                </div>
                <div class="page-actions">
                    <a href="user_list.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Daftar User
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- User Info Card -->
            <div class="col-lg-4 mb-4">
                <div class="card user-info-card fade-in-up">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Informasi User
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="user-avatar-large mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-2"><?= htmlspecialchars($user['name']) ?></h5>
                        <div class="role-badge role-<?= $user['role'] ?> mb-3">
                            <?php
                            $role_icons = [
                                'admin' => 'fas fa-user-shield',
                                'panitia' => 'fas fa-users-cog',
                                'berqurban' => 'fas fa-hand-holding-heart',
                                'warga' => 'fas fa-user'
                            ];
                            ?>
                            <i class="<?= $role_icons[$user['role']] ?> me-1"></i>
                            <?= ucfirst($user['role']) ?>
                        </div>

                        <div class="user-details">
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">NIK</span>
                                    <span class="detail-value"><?= htmlspecialchars($user['nik']) ?></span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-at"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Username</span>
                                    <span class="detail-value"><?= htmlspecialchars($user['username']) ?></span>
                                </div>
                            </div>

                            <?php if ($user['no_hp']): ?>
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">No. HP</span>
                                        <span class="detail-value"><?= htmlspecialchars($user['no_hp']) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form Card -->
            <div class="col-lg-8 mb-4">
                <div class="card edit-form-card fade-in-up">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Form Edit User
                        </h5>
                        <p class="card-subtitle">Perbarui informasi pengguna di bawah ini</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="editUserForm">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-2"></i>Nama Lengkap
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= htmlspecialchars($user['name']) ?>" required>
                                    <div class="form-text">Masukkan nama lengkap pengguna</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-at me-2"></i>Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?= htmlspecialchars($user['username']) ?>" required>
                                    <div class="form-text">Username untuk login ke sistem</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password Baru
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Kosongkan jika tidak ingin mengubah">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">
                                        <i class="fas fa-user-tag me-2"></i>Role
                                    </label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>
                                            <i class="fas fa-user-shield"></i> Admin
                                        </option>
                                        <option value="panitia" <?= $user['role'] == 'panitia' ? 'selected' : '' ?>>
                                            <i class="fas fa-users-cog"></i> Panitia
                                        </option>
                                        <option value="berqurban" <?= $user['role'] == 'berqurban' ? 'selected' : '' ?>>
                                            <i class="fas fa-hand-holding-heart"></i> Berqurban
                                        </option>
                                        <option value="warga" <?= $user['role'] == 'warga' ? 'selected' : '' ?>>
                                            <i class="fas fa-user"></i> Warga
                                        </option>
                                    </select>
                                    <div class="form-text">Tentukan peran pengguna dalam sistem</div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">
                                        <i class="fas fa-phone me-2"></i>No. HP
                                    </label>
                                    <input type="tel" class="form-control" id="no_hp" name="no_hp"
                                        value="<?= htmlspecialchars($user['no_hp']) ?>"
                                        placeholder="Contoh: 08123456789">
                                    <div class="form-text">Nomor HP untuk komunikasi</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="alamat" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                    </label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                        placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($user['alamat']) ?></textarea>
                                    <div class="form-text">Alamat tempat tinggal</div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions mt-4">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-2"></i>Reset Form
                                        </button>
                                    </div>
                                    <div>
                                        <a href="user_list.php" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                        <button type="submit" name="update" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Actions Card -->
        <div class="row">
            <div class="col-12">
                <div class="card actions-card fade-in-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Aksi Tambahan</h6>
                                <small class="text-muted">Aksi lain yang dapat dilakukan untuk user ini</small>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-outline-info me-2" onclick="viewUserHistory()">
                                    <i class="fas fa-history me-2"></i>Lihat Riwayat
                                </button>
                                <button type="button" class="btn btn-outline-warning me-2" onclick="resetPassword()">
                                    <i class="fas fa-key me-2"></i>Reset Password
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-2"></i>Hapus User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Konfirmasi Hapus User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus user <strong><?= htmlspecialchars($user['name']) ?></strong>?
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="delete_user.php?nik=<?= $user['nik'] ?>" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Reset form
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form? Semua perubahan akan hilang.')) {
                document.getElementById('editUserForm').reset();
            }
        }

        // Confirm delete
        function confirmDelete() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // View user history (placeholder)
        function viewUserHistory() {
            alert('Fitur riwayat user akan segera tersedia!');
        }

        // Reset password (placeholder)
        function resetPassword() {
            if (confirm('Apakah Anda yakin ingin mereset password user ini?')) {
                alert('Fitur reset password akan segera tersedia!');
            }
        }

        // Form validation
        document.getElementById('editUserForm').addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const username = document.getElementById('username').value.trim();
            const role = document.getElementById('role').value;

            if (!name || !username || !role) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return false;
            }

            // Username validation
            if (username.length < 3) {
                e.preventDefault();
                alert('Username minimal 3 karakter!');
                return false;
            }

            // Password validation (if provided)
            const password = document.getElementById('password').value;
            if (password && password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });

        // Auto-dismiss alerts
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>