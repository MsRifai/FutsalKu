<?php
require_once 'config/config.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . base_url('admin/dashboard.php'));
    } else {
        header("Location: " . base_url('user/dashboard.php'));
    }
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        $user  = mysqli_fetch_assoc($query);

        if ($user) {
            $password_valid = false;

            // Cek password hash bcrypt
            if (password_verify($password, $user['password'])) {
                $password_valid = true;
            } 
            // Fallback untuk MD5 legacy
            else if (md5($password) === $user['password']) {
                $password_valid = true;
                // Auto upgrade ke password_hash
                $new_hash = password_hash($password, PASSWORD_BCRYPT);
                mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id_user='{$user['id_user']}'");
            }

            if ($password_valid) {
                $_SESSION['login'] = true;
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: " . base_url('admin/dashboard.php'));
                } else {
                    header("Location: " . base_url('user/dashboard.php'));
                }
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center my-4">
    <div class="col-md-6 col-lg-5">
      <div class="card auth-card">
        <div class="text-center mb-4">
          <div class="bg-danger bg-opacity-10 text-maroon d-inline-flex p-3 rounded-circle mb-3">
            <i class="bi bi-person-lock fs-1" style="color:#be123c;"></i>
          </div>
          <h3 class="fw-bold text-dark mb-1">Selamat Datang Kembali</h3>
          <p class="text-secondary small">Masuk ke akun FutsalKu Anda untuk booking lapangan</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($error) ?></div>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
          <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>Registrasi berhasil! Silakan login di bawah.</div>
          </div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="mb-3">
            <label class="form-label fw-semibold text-dark small">Username</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-person"></i></span>
              <input type="text" name="username" class="form-control form-control-custom border-start-0" placeholder="Masukkan username Anda" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label fw-semibold text-dark small mb-0">Password</label>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control form-control-custom border-start-0" placeholder="••••••••" required>
            </div>
          </div>

          <button type="submit" name="login" class="btn btn-primary-custom w-100 py-3 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Akun
          </button>
        </form>

        <div class="text-center small text-secondary mt-3">
          Belum punya akun? <a href="register.php" class="fw-semibold text-decoration-none" style="color:#be123c;">Daftar Sekarang</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
