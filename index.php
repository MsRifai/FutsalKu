<?php
require_once 'config/config.php';
include 'includes/header.php';

// Fetch all available fields
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id_lapangan ASC");

// Fetch stats for hero
$total_lapangan = mysqli_num_rows($query_lapangan);
$stat_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status='lunas'"))['total'] ?? 0;
$stat_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'] ?? 0;
?>

<!-- Hero Section Maroon Edition -->
<section class="hero-section">
  <div class="container position-relative" style="z-index: 2;">
    <div class="row align-items-center gy-5">
      <div class="col-lg-7">
        <span class="badge bg-danger bg-opacity-25 text-white border border-danger border-opacity-50 px-3 py-2 rounded-pill mb-3">
          <i class="bi bi-star-fill text-warning me-1"></i> Platform Booking Lapangan #1
        </span>
        <h1 class="hero-title">Sewa Lapangan Futsal <span>Mudah, Cepat & Praktis</span></h1>
        <p class="hero-subtitle">Main futsal tanpa ribet antre! Cek ketersediaan lapangan secara realtime, pilih slot waktu favoritmu, dan booking langsung dari gadget-mu sekarang.</p>
        
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="#daftar-lapangan" class="btn btn-primary-custom btn-lg px-4">
            <i class="bi bi-calendar-event me-2"></i>Pesan Lapangan Sekarang
          </a>
          <?php if (!isset($_SESSION['login'])): ?>
            <a href="register.php" class="btn btn-outline-custom btn-lg px-4">
              <i class="bi bi-person-plus me-2"></i>Daftar Akun Gratis
            </a>
          <?php endif; ?>
        </div>

        <div class="row pt-4 border-top border-white border-opacity-10 text-white">
          <div class="col-4">
            <div class="fs-3 fw-bold text-maroon" style="color:#fb7185 !important;"><?= $total_lapangan ?>+</div>
            <div class="small text-secondary">Lapangan Pilihan</div>
          </div>
          <div class="col-4">
            <div class="fs-3 fw-bold text-maroon" style="color:#fb7185 !important;"><?= $stat_booking + 150 ?>+</div>
            <div class="small text-secondary">Pertandingan Selesai</div>
          </div>
          <div class="col-4">
            <div class="fs-3 fw-bold text-maroon" style="color:#fb7185 !important;"><?= $stat_user + 50 ?>+</div>
            <div class="small text-secondary">Member Aktif</div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 text-center">
        <div class="position-relative d-inline-block">
          <img src="uploads/foto_lapangan/lapangan_sintetis.jpg" alt="Futsal Court" class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25" style="max-height: 420px; object-fit: cover;">
          <div class="position-absolute bottom-0 start-0 translate-middle-y bg-dark bg-opacity-80 backdrop-blur text-white p-3 rounded-3 border border-secondary border-opacity-50 shadow text-start ms-n3 mb-3 d-none d-sm-block">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-maroon text-white p-2 rounded-circle" style="background-color:#be123c !important;">
                <i class="bi bi-check-circle-fill fs-4"></i>
              </div>
              <div>
                <div class="fw-bold small">Persetujuan Cepat</div>
                <div class="text-secondary" style="font-size:0.75rem;">Konfirmasi pembayaran instan</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Keunggulan Section -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <span class="text-maroon fw-bold text-uppercase small tracking-wider" style="color:#be123c !important;">Mengapa Memilih Kami</span>
      <h2 class="fw-bold text-dark mt-1">Fasilitas & Layanan Olahraga Terbaik</h2>
      <p class="text-secondary">Kami berkomitmen menyajikan pengalaman bermain futsal & olahraga terbaik bagi tim Anda.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 rounded-4 bg-light border border-light h-100 transition-hover">
          <div class="stat-icon maroon mb-3">
            <i class="bi bi-award-fill fs-3 text-maroon"></i>
          </div>
          <h5 class="fw-bold text-dark">Standar Internasional</h5>
          <p class="text-secondary small mb-0">Lantai rumput sintetis, vinyl wood, dan matras interlock berkualitas tinggi yang aman dan nyaman bagi persendian kaki.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 rounded-4 bg-light border border-light h-100 transition-hover">
          <div class="stat-icon blue mb-3">
            <i class="bi bi-clock-history fs-3 text-primary"></i>
          </div>
          <h5 class="fw-bold text-dark">Booking 24/7 Realtime</h5>
          <p class="text-secondary small mb-0">Cek ketersediaan jam dan tanggal kapan saja tanpa harus menelepon atau datang langsung ke lokasi arena.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 rounded-4 bg-light border border-light h-100 transition-hover">
          <div class="stat-icon amber mb-3">
            <i class="bi bi-lightbulb-fill fs-3 text-warning"></i>
          </div>
          <h5 class="fw-bold text-dark">Pencahayaan LED Terang</h5>
          <p class="text-secondary small mb-0">Pencahayaan stadion LED bebas bayangan yang memberikan visibilitas maksimal bahkan saat pertandingan malam hari.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Daftar Lapangan Section -->
<section id="daftar-lapangan" class="py-5 bg-light">
  <div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
      <div>
        <span class="text-maroon fw-bold text-uppercase small tracking-wider" style="color:#be123c !important;">Pilihan Lapangan</span>
        <h2 class="fw-bold text-dark mb-0">Temukan Arena Pertandinganmu</h2>
      </div>
      <div class="mt-3 mt-md-0">
        <p class="text-secondary mb-0">Semua lapangan dirawat secara berkala demi kenyamanan Anda.</p>
      </div>
    </div>

    <div class="row g-4">
      <?php if (mysqli_num_rows($query_lapangan) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($query_lapangan)): 
          $foto = !empty($row['foto']) ? $row['foto'] : 'lapangan_sintetis.jpg';
        ?>
          <div class="col-lg-4 col-md-6">
            <div class="card-lapangan">
              <div class="card-lapangan-img-wrapper">
                <span class="badge-jenis"><i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($row['jenis']) ?></span>
                <img src="uploads/foto_lapangan/<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($row['nama_lapangan']) ?>" onerror="this.src='uploads/foto_lapangan/lapangan_sintetis.jpg'">
              </div>
              <div class="card-lapangan-body">
                <h4 class="card-lapangan-title"><?= htmlspecialchars($row['nama_lapangan']) ?></h4>
                <p class="card-lapangan-desc">
                  <?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : 'Lapangan futsal profesional lengkap dengan jaring pengaman, bola match ball, dan tribune penonton.' ?>
                </p>
                <div class="card-lapangan-footer">
                  <div>
                    <div class="harga-tag" style="color:#881337;"><?= format_rupiah($row['harga_per_jam']) ?></div>
                    <div class="harga-unit">per jam main</div>
                  </div>
                  <?php if (isset($_SESSION['login']) && $_SESSION['role'] == 'user'): ?>
                    <a href="user/booking.php?id_lapangan=<?= $row['id_lapangan'] ?>" class="btn btn-primary-custom px-3 py-2 text-white">
                      <i class="bi bi-calendar-plus me-1"></i> Booking
                    </a>
                  <?php else: ?>
                    <a href="login.php" class="btn btn-primary-custom px-3 py-2 text-white">
                      <i class="bi bi-calendar-plus me-1"></i> Booking
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
          <p class="text-muted">Belum ada lapangan yang didaftarkan.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-dark text-white">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <span class="text-maroon fw-bold text-uppercase small tracking-wider" style="color:#fb7185 !important;">Cara Kerja</span>
      <h2 class="fw-bold text-white mt-1">3 Langkah Mudah Booking Lapangan</h2>
    </div>

    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="p-4 border border-secondary border-opacity-25 rounded-4 bg-opacity-10 bg-secondary h-100">
          <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle fs-3 fw-bold mb-3" style="width: 60px; height: 60px; background-color: #be123c;">1</div>
          <h5 class="fw-bold">Pilih Lapangan & Jam</h5>
          <p class="text-secondary small mb-0">Tentukan lapangan favorit Anda dan sesuaikan tanggal serta jam mulai/selesai yang masih kosong.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 border border-secondary border-opacity-25 rounded-4 bg-opacity-10 bg-secondary h-100">
          <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle fs-3 fw-bold mb-3" style="width: 60px; height: 60px;">2</div>
          <h5 class="fw-bold">Transfer & Upload Bukti</h5>
          <p class="text-secondary small mb-0">Lakukan pembayaran sesuai nominal total dan unggah foto/screenshot bukti transfer di menu riwayat.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 border border-secondary border-opacity-25 rounded-4 bg-opacity-10 bg-secondary h-100">
          <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle fs-3 fw-bold mb-3" style="width: 60px; height: 60px;">3</div>
          <h5 class="fw-bold">Konfirmasi & Tunjukkan Struk</h5>
          <p class="text-secondary small mb-0">Admin akan memverifikasi pembayaran Anda. Tunjukkan tiket/invoice digital saat masuk ke lapangan!</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Banner Maroon -->
<section class="py-5 text-white text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #881337 0%, #be123c 100%) !important;">
  <div class="container py-3">
    <h2 class="fw-extrabold mb-3">Siap Bertanding Hari Ini?</h2>
    <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 600px;">Jangan biarkan tim lawan mengambil slot favoritmu. Kumpulkan timmu dan pesan lapangan sekarang!</p>
    <a href="user/dashboard.php" class="btn btn-dark btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg">
      <i class="bi bi-lightning-fill text-warning me-2"></i>Pesan Slot Lapangan Now
    </a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
