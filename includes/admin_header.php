<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

// Pastikan hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('login.php'));
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - FutsalKu (Maroon Edition)</title>
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
<body class="bg-light">

<div class="admin-wrapper">
  <!-- Sidebar Admin Maroon -->
  <aside class="admin-sidebar shadow">
    <div class="brand-header d-flex align-items-center gap-2" style="background: #1e050c; border-bottom: 1px solid rgba(255,255,255,0.08);">
      <div class="p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #be123c, #881337); width:38px; height:38px;">
        <i class="bi bi-futbol fs-5"></i>
      </div>
      <div>
        <span class="fw-bold text-white fs-5">Futsal<span style="color:#fb7185;">Ku</span></span>
        <div class="text-uppercase fw-semibold" style="font-size:0.65rem; color:#fda4af; letter-spacing:1px;">ADMIN PANEL</div>
      </div>
    </div>

    <div class="admin-nav">
      <div class="text-uppercase text-secondary fs-7 fw-bold px-3 mb-2 mt-2" style="font-size: 0.7rem; letter-spacing: 1px; color:#9ca3af !important;">Navigasi Utama</div>
      
      <div class="nav-item">
        <a href="<?= base_url('admin/dashboard.php') ?>" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
          <i class="bi bi-speedometer2"></i> Dashboard Overview
        </a>
      </div>

      <div class="nav-item">
        <a href="<?= base_url('admin/kelola_lapangan.php') ?>" class="nav-link <?= (in_array($current_page, ['kelola_lapangan.php', 'tambah_lapangan.php', 'edit_lapangan.php'])) ? 'active' : '' ?>">
          <i class="bi bi-grid-3x3-gap-fill"></i> Kelola Lapangan
        </a>
      </div>

      <div class="nav-item">
        <a href="<?= base_url('admin/kelola_booking.php') ?>" class="nav-link <?= ($current_page == 'kelola_booking.php') ? 'active' : '' ?>">
          <i class="bi bi-calendar-check"></i> Kelola Transaksi Booking
        </a>
      </div>

      <div class="nav-item">
        <a href="<?= base_url('admin/kelola_user.php') ?>" class="nav-link <?= ($current_page == 'kelola_user.php') ? 'active' : '' ?>">
          <i class="bi bi-people-fill"></i> Data Pelanggan
        </a>
      </div>

      <div class="nav-item">
        <a href="<?= base_url('admin/laporan.php') ?>" class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : '' ?>">
          <i class="bi bi-file-earmark-bar-graph"></i> Laporan Keuangan
        </a>
      </div>

      <div class="text-uppercase text-secondary fs-7 fw-bold px-3 mt-4 mb-2" style="font-size: 0.7rem; letter-spacing: 1px; color:#9ca3af !important;">Pintas & Akses</div>
      
      <div class="nav-item">
        <a href="<?= base_url('index.php') ?>" target="_blank" class="nav-link">
          <i class="bi bi-box-arrow-up-right"></i> Lihat Website Utama
        </a>
      </div>

      <div class="nav-item">
        <a href="<?= base_url('logout.php') ?>" onclick="return confirm('Yakin ingin keluar dari Admin Panel?')" class="nav-link text-danger">
          <i class="bi bi-box-arrow-right text-danger"></i> Logout
        </a>
      </div>
    </div>

    <div class="p-3 mt-auto border-top border-secondary border-opacity-25" style="background:#111827;">
      <div class="d-flex align-items-center gap-2">
        <div class="text-white rounded-circle p-2 d-flex align-items-center justify-content-center fw-bold" style="width:38px; height:38px; background: linear-gradient(135deg, #be123c, #881337);">
          A
        </div>
        <div>
          <div class="fw-bold text-white small"><?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?></div>
          <div class="text-muted" style="font-size:0.75rem;">Administrator System</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content Admin -->
  <main class="admin-content">
    <!-- Top Bar Bar Admin -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm border">
      <div class="d-flex align-items-center gap-3">
        <div class="d-md-none">
          <button class="btn btn-outline-dark btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileDrawer">
            <i class="bi bi-list fs-5"></i>
          </button>
        </div>
        <div>
          <span class="badge bg-danger bg-opacity-10 text-maroon border border-danger border-opacity-25 px-3 py-1 rounded-pill small fw-semibold" style="color:#be123c !important;">
            <i class="bi bi-shield-lock-fill me-1"></i> Admin Panel FutsalKu
          </span>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="text-end d-none d-sm-block">
          <div class="fw-bold text-dark small"><?= date('l, d F Y') ?></div>
          <div class="text-secondary fs-7" style="font-size:0.75rem;">Waktu Server: WIB</div>
        </div>
        <a href="<?= base_url('logout.php') ?>" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="return confirm('Keluar dari sistem admin?')">
          <i class="bi bi-power me-1"></i> Logout
        </a>
      </div>
    </div>
