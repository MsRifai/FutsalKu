<?php
require_once 'config/config.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$error = '';

if (isset($_POST['register'])) {
    $username = clean($_POST['username']);
    $nama     = clean($_POST['nama']);
    $email    = clean($_POST['email']);
    $no_hp    = clean($_POST['no_hp'] ?? '');
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = 'user';

    if (empty($username) || empty($nama) || empty($email) || empty($password)) {
        $error = "Semua kolom bertanda * wajib diisi!";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter!";
    } else {
        // Cek username unik
        $check_user = mysqli_query($conn, "SELECT id_user FROM users WHERE username='$username'");
        if (mysqli_num_rows($check_user) > 0) {
            $error = "Username '$username' sudah terdaftar! Gunakan username lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, password, nama, email, no_hp, role) 
                    VALUES ('$username', '$hashed_password', '$nama', '$email', '$no_hp', '$role')";

            if (mysqli_query($conn, $sql)) {
                header("Location: " . base_url('login.php?registered=success'));
                exit;
            } else {
                $error = "Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center my-3">
    <div class="col-md-7 col-lg-6">
      <div class="card auth-card">
        <div class="text-center mb-4">
          <div class="bg-danger bg-opacity-10 text-maroon d-inline-flex p-3 rounded-circle mb-3">
            <i class="bi bi-person-plus-fill fs-1" style="color:#be123c;"></i>
          </div>
          <h3 class="fw-bold text-dark mb-1">Buat Akun Baru</h3>
          <p class="text-secondary small">Bergabunglah dengan FutsalKu untuk kemudahan booking lapangan</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($error) ?></div>
          </div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="row g-3">
            <div class="col-md-6 mb-2">
              <label class="form-label fw-semibold text-dark small">Username *</label>
              <input type="text" name="username" class="form-control form-control-custom" placeholder="Username unik" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="col-md-6 mb-2">
              <label class="form-label fw-semibold text-dark small">Nama Lengkap *</label>
              <input type="text" name="nama" class="form-control form-control-custom" placeholder="Nama Anda" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>

            <div class="col-md-6 mb-2">
              <label class="form-label fw-semibold text-dark small">Email *</label>
              <input type="email" name="email" class="form-control form-control-custom" placeholder="email@contoh.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="col-md-6 mb-2">
              <label class="form-label fw-semibold text-dark small">No. WhatsApp / HP</label>
              <input type="text" name="no_hp" class="form-control form-control-custom" placeholder="08123456789" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold text-dark small">Password *</label>
              <input type="password" name="password" class="form-control form-control-custom" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold text-dark small">Konfirmasi Password *</label>
              <input type="password" name="confirm_password" class="form-control form-control-custom" placeholder="Ulangi password" required>
            </div>
          </div>

          <button type="submit" name="register" class="btn btn-primary-custom w-100 py-3 mt-2 mb-3">
            <i class="bi bi-check-circle me-2"></i>Daftar Akun Sekarang
          </button>
        </form>

        <div class="text-center small text-secondary">
          Sudah punya akun? <a href="login.php" class="fw-semibold text-decoration-none" style="color:#be123c;">Masuk di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
