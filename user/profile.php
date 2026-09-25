<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('admin/dashboard.php'));
    exit;
}

$id_user = $_SESSION['id_user'];
$error = '';
$success = '';

// Update profil
if (isset($_POST['update_profile'])) {
    $nama  = clean($_POST['nama']);
    $email = clean($_POST['email']);
    $no_hp = clean($_POST['no_hp']);

    if (empty($nama) || empty($email)) {
        $error = "Nama dan Email wajib diisi!";
    } else {
        $sql = "UPDATE users SET nama='$nama', email='$email', no_hp='$no_hp' WHERE id_user='$id_user'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['nama'] = $nama;
            $success = "Data profil berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}

// Update password
if (isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    $user_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id_user='$id_user'"));

    $pass_valid = password_verify($old_pass, $user_q['password']) || (md5($old_pass) === $user_q['password']);

    if (!$pass_valid) {
        $error = "Password lama yang Anda masukkan salah!";
    } elseif ($new_pass !== $confirm) {
        $error = "Konfirmasi password baru tidak cocok!";
    } elseif (strlen($new_pass) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
        if (mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id_user='$id_user'")) {
            $success = "Password berhasil diubah!";
        } else {
            $error = "Gagal mengubah password.";
        }
    }
}

// Fetch user fresh data
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'"));

include '../includes/header.php';
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="width:60px; height:60px; background-color:#be123c;">
          <i class="bi bi-person-circle fs-2"></i>
        </div>
        <div>
          <h3 class="fw-bold text-dark mb-0">Profil Akun Saya</h3>
          <p class="text-secondary small mb-0">Kelola informasi pribadi dan keamanan kata sandi Anda.</p>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          <div><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
          <i class="bi bi-check-circle-fill fs-5"></i>
          <div><?= htmlspecialchars($success) ?></div>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Edit Profile -->
        <div class="col-md-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill me-2" style="color:#be123c;"></i> Edit Informasi Diri</h5>
            <form method="post" action="">
              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Username (Tetap)</label>
                <input type="text" class="form-control form-control-custom bg-light" value="<?= htmlspecialchars($user['username']) ?>" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control form-control-custom" value="<?= htmlspecialchars($user['nama']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Email *</label>
                <input type="email" name="email" class="form-control form-control-custom" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold text-dark small">No. WhatsApp / HP</label>
                <input type="text" name="no_hp" class="form-control form-control-custom" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" placeholder="08123456789">
              </div>

              <button type="submit" name="update_profile" class="btn btn-primary-custom w-100 py-2">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
              </button>
            </form>
          </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock-fill me-2" style="color:#be123c;"></i> Ubah Password</h5>
            <form method="post" action="">
              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Password Saat Ini *</label>
                <input type="password" name="old_password" class="form-control form-control-custom" placeholder="••••••••" required>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Password Baru *</label>
                <input type="password" name="new_password" class="form-control form-control-custom" placeholder="Minimal 6 karakter" required>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold text-dark small">Konfirmasi Password Baru *</label>
                <input type="password" name="confirm_password" class="form-control form-control-custom" placeholder="Ulangi password baru" required>
              </div>

              <button type="submit" name="change_password" class="btn btn-outline-dark w-100 py-2">
                <i class="bi bi-key-fill me-1"></i> Perbarui Password
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
