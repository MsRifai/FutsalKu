<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FutsalKu - Sewa Lapangan Olahraga Premium</title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('index.php') ?>">
      <i class="bi bi-futbol fs-3 text-maroon"></i>
      <span>Futsal<span style="color:#fb7185;">Ku</span></span>
    </a>
    <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <i class="bi bi-list fs-2 text-white"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/dashboard.php') ?>"><i class="bi bi-speedometer2 me-1"></i> Admin Panel</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="<?= base_url('user/dashboard.php') ?>">
                <i class="bi bi-grid-fill me-1"></i> Cari Lapangan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($current_page == 'history.php') ? 'active' : '' ?>" href="<?= base_url('user/history.php') ?>">
                <i class="bi bi-clock-history me-1"></i> Riwayat Booking
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($current_page == 'profile.php') ? 'active' : '' ?>" href="<?= base_url('user/profile.php') ?>">
                <i class="bi bi-person-circle me-1"></i> Profil Saya
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item ms-lg-2">
            <a class="nav-link btn-nav-action px-3 py-2 text-white" href="<?= base_url('logout.php') ?>" onclick="return confirm('Yakin ingin keluar dari akun Anda?')">
              <i class="bi bi-box-arrow-right me-1"></i> Logout (<?= htmlspecialchars($_SESSION['username']) ?>)
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('index.php') ?>">Beranda</a>
          </li>
          <li class="nav-item ms-lg-2">
            <a class="nav-link text-white" href="<?= base_url('login.php') ?>">Masuk</a>
          </li>
          <li class="nav-item ms-lg-1">
            <a class="btn btn-primary-custom px-4 text-white" href="<?= base_url('register.php') ?>">Daftar Sekarang</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="main-wrapper">
