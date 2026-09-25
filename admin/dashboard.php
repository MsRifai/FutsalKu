<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

include '../includes/admin_header.php';

// Ambil statistik data
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM users WHERE role='user'"))['jumlah'];
$total_lapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM lapangan"))['jumlah'];
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM booking"))['jumlah'];
$pending_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM booking WHERE status='menunggu'"))['jumlah'];
$lunas_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM booking WHERE status='lunas'"))['jumlah'];
$ditolak_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM booking WHERE status='ditolak'"))['jumlah'];

// Hitung Pendapatan Lunas
$total_revenue_q = mysqli_query($conn, "
    SELECT SUM(CASE WHEN b.total_harga > 0 THEN b.total_harga ELSE (TIME_TO_SEC(TIMEDIFF(b.jam_selesai, b.jam_mulai))/3600 * l.harga_per_jam) END) as total 
    FROM booking b
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan
    WHERE b.status = 'lunas'
");
$total_revenue = mysqli_fetch_assoc($total_revenue_q)['total'] ?? 0;

// Booking Terbaru (5 data terakhir)
$recent_bookings = mysqli_query($conn, "
    SELECT b.*, u.nama as nama_user, l.nama_lapangan 
    FROM booking b 
    JOIN users u ON b.id_user = u.id_user 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    ORDER BY b.id_booking DESC LIMIT 5
");
?>

<!-- Banner Sambutan Admin Maroon -->
<div class="p-4 p-md-5 rounded-4 text-white position-relative overflow-hidden mb-4 shadow" style="background: linear-gradient(135deg, #1e050c 0%, #3b0713 50%, #111827 100%);">
  <div class="row align-items-center position-relative" style="z-index: 2;">
    <div class="col-md-8">
      <span class="badge bg-danger bg-opacity-25 text-white border border-danger border-opacity-50 px-3 py-2 rounded-pill mb-3">
        <i class="bi bi-speedometer2 me-1"></i> Dashboard Overview
      </span>
      <h2 class="fw-bold mb-2">Selamat Datang, Administrator <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?>! 👋</h2>
      <p class="text-secondary mb-0">Pantau performa pendapatan sewa lapangan, verifikasi pembayaran pelanggan, dan kelola operasional FutsalKu secara realtime.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
      <a href="tambah_lapangan.php" class="btn btn-primary-custom px-4 py-2 me-2">
        <i class="bi bi-plus-circle me-1"></i> Lapangan Baru
      </a>
      <a href="kelola_booking.php" class="btn btn-outline-custom px-3 py-2">
        <i class="bi bi-calendar-check me-1"></i> Verifikasi
      </a>
    </div>
  </div>
</div>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-sm-6">
    <div class="stat-card border-0 shadow-sm">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-title">Total Pendapatan</div>
          <div class="stat-value text-maroon" style="color:#be123c !important;"><?= format_rupiah($total_revenue) ?></div>
        </div>
        <div class="stat-icon maroon mb-0">
          <i class="bi bi-cash-stack"></i>
        </div>
      </div>
      <div class="mt-3 small text-muted"><i class="bi bi-check-circle-fill text-danger me-1"></i> Transaksi status LUNAS</div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6">
    <div class="stat-card border-0 shadow-sm">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-title">Total Booking</div>
          <div class="stat-value"><?= $total_booking ?></div>
        </div>
        <div class="stat-icon blue mb-0">
          <i class="bi bi-calendar-event"></i>
        </div>
      </div>
      <div class="mt-3 small text-muted"><i class="bi bi-clock-history text-primary me-1"></i> <?= $pending_booking ?> permohonan baru</div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6">
    <div class="stat-card border-0 shadow-sm">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-title">Jumlah Lapangan</div>
          <div class="stat-value"><?= $total_lapangan ?></div>
        </div>
        <div class="stat-icon amber mb-0">
          <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
      </div>
      <div class="mt-3 small text-muted"><i class="bi bi-building text-warning me-1"></i> Lapangan aktif disewakan</div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6">
    <div class="stat-card border-0 shadow-sm">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-title">Pelanggan Terdaftar</div>
          <div class="stat-value"><?= $total_user ?></div>
        </div>
        <div class="stat-icon rose mb-0">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>
      <div class="mt-3 small text-muted"><i class="bi bi-person-check-fill text-danger me-1"></i> Akun pengguna aktif</div>
    </div>
  </div>
</div>

<!-- Status Distribution & Quick Actions -->
<div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2" style="color:#be123c;"></i> Distribusi Status Booking</h6>
        <a href="laporan.php" class="small text-decoration-none fw-semibold" style="color:#be123c;">Lihat Laporan Detail <i class="bi bi-arrow-right"></i></a>
      </div>

      <?php
        $perc_lunas = $total_booking > 0 ? round(($lunas_booking / $total_booking) * 100) : 0;
        $perc_pending = $total_booking > 0 ? round(($pending_booking / $total_booking) * 100) : 0;
        $perc_ditolak = $total_booking > 0 ? round(($ditolak_booking / $total_booking) * 100) : 0;
      ?>

      <div class="progress mb-3 rounded-pill" style="height: 18px;">
        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $perc_lunas ?>%; background-color:#be123c !important;" title="Lunas: <?= $perc_lunas ?>%">Lunas (<?= $perc_lunas ?>%)</div>
        <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: <?= $perc_pending ?>%;" title="Menunggu: <?= $perc_pending ?>%">Menunggu (<?= $perc_pending ?>%)</div>
        <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= $perc_ditolak ?>%;" title="Ditolak/Batal: <?= $perc_ditolak ?>%">Lainnya</div>
      </div>

      <div class="row g-2 text-center pt-2 small text-secondary">
        <div class="col-4">
          <div class="p-2 rounded bg-light border">
            <div class="fw-bold text-dark fs-6"><?= $lunas_booking ?></div>
            <div class="fs-7"><i class="bi bi-circle-fill text-danger me-1" style="color:#be123c !important;"></i> Lunas</div>
          </div>
        </div>
        <div class="col-4">
          <div class="p-2 rounded bg-light border">
            <div class="fw-bold text-dark fs-6"><?= $pending_booking ?></div>
            <div class="fs-7"><i class="bi bi-circle-fill text-warning me-1"></i> Menunggu Admin</div>
          </div>
        </div>
        <div class="col-4">
          <div class="p-2 rounded bg-light border">
            <div class="fw-bold text-dark fs-6"><?= $ditolak_booking ?></div>
            <div class="fs-7"><i class="bi bi-circle-fill text-secondary me-1"></i> Ditolak / Batal</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
      <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge-fill me-2" style="color:#be123c;"></i> Akses Cepat Admin</h6>
      <div class="d-flex flex-column gap-2">
        <a href="kelola_booking.php?status=menunggu" class="btn btn-outline-warning text-start p-3 rounded-3 d-flex align-items-center justify-content-between">
          <div>
            <div class="fw-bold text-dark small">Perlu Verifikasi</div>
            <div class="fs-7 text-muted"><?= $pending_booking ?> Permohonan baru</div>
          </div>
          <i class="bi bi-chevron-right fs-5"></i>
        </a>
        <a href="laporan.php" class="btn btn-outline-danger text-start p-3 rounded-3 d-flex align-items-center justify-content-between" style="border-color:#be123c; color:#be123c;">
          <div>
            <div class="fw-bold text-dark small">Cetak Laporan Keuangan</div>
            <div class="fs-7 text-muted">Rekap transaksi bulanan</div>
          </div>
          <i class="bi bi-printer-fill fs-5"></i>
        </a>
        <a href="kelola_lapangan.php" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex align-items-center justify-content-between">
          <div>
            <div class="fw-bold text-dark small">Kelola Lapangan & Harga</div>
            <div class="fs-7 text-muted"><?= $total_lapangan ?> Lapangan terdaftar</div>
          </div>
          <i class="bi bi-sliders fs-5"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Table Booking Terbaru -->
<div class="custom-table-card shadow-sm mb-4">
  <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2" style="color:#be123c;"></i> 5 Transaksi Booking Terbaru</h6>
    <a href="kelola_booking.php" class="btn btn-link btn-sm fw-semibold text-decoration-none" style="color:#be123c;">Lihat Semua Data <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="table-responsive">
    <table class="table custom-table align-middle">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Pelanggan</th>
          <th>Lapangan</th>
          <th>Tanggal & Jam</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($recent_bookings) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($recent_bookings)): 
            $st = $row['status'];
          ?>
            <tr>
              <td><span class="fw-bold text-dark">#BK-<?= sprintf('%04d', $row['id_booking']) ?></span></td>
              <td><span class="fw-semibold"><?= htmlspecialchars($row['nama_user']) ?></span></td>
              <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
              <td>
                <div class="small fw-semibold"><?= format_tanggal($row['tanggal']) ?></div>
                <div class="fs-7 text-muted"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?> WIB</div>
              </td>
              <td>
                <?php if ($st == 'menunggu'): ?>
                  <span class="badge-status badge-status-menunggu">Menunggu</span>
                <?php elseif ($st == 'disetujui'): ?>
                  <span class="badge-status badge-status-disetujui">Disetujui</span>
                <?php elseif ($st == 'lunas'): ?>
                  <span class="badge-status badge-status-lunas">Lunas</span>
                <?php elseif ($st == 'ditolak'): ?>
                  <span class="badge-status badge-status-ditolak">Ditolak</span>
                <?php else: ?>
                  <span class="badge-status badge-status-batal">Batal</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="kelola_booking.php" class="btn btn-sm btn-light border">Detail</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">Belum ada booking terbaru.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>