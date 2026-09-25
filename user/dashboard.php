<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('admin/dashboard.php'));
    exit;
}

// Search and Filter handling
$search = clean($_GET['search'] ?? '');
$jenis  = clean($_GET['jenis'] ?? '');

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(nama_lapangan LIKE '%$search%' OR jenis LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}
if (!empty($jenis)) {
    $where_clauses[] = "jenis = '$jenis'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";
$query = "SELECT * FROM lapangan $where_sql ORDER BY id_lapangan ASC";
$result = mysqli_query($conn, $query);

// Get distinct categories for filter
$query_categories = mysqli_query($conn, "SELECT DISTINCT jenis FROM lapangan");

include '../includes/header.php';
?>

<div class="container py-4">
  <!-- Welcome Banner Maroon -->
  <div class="p-4 p-md-5 rounded-4 text-white position-relative overflow-hidden mb-5" style="background: linear-gradient(135deg, #1e050c 0%, #111827 100%);">
    <div class="position-relative" style="z-index: 2;">
      <span class="badge bg-danger bg-opacity-25 text-white border border-danger border-opacity-50 px-3 py-2 rounded-pill mb-3">
        <i class="bi bi-shield-check me-1"></i> Sesi Aktif
      </span>
      <h2 class="fw-bold mb-2">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?>! 👋</h2>
      <p class="text-secondary mb-0 max-w-600">Temukan arena pertandingan terbaik Anda. Pilih lapangan, tentukan jadwal main, dan booking secara instant!</p>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form method="get" action="" class="row g-2 align-items-center">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama lapangan atau fasilitas..." value="<?= htmlspecialchars($search) ?>">
        </div>
      </div>
      <div class="col-md-4">
        <select name="jenis" class="form-select border-0 bg-light">
          <option value="">-- Semua Jenis Lapangan --</option>
          <?php while($cat = mysqli_fetch_assoc($query_categories)): ?>
            <option value="<?= htmlspecialchars($cat['jenis']) ?>" <?= ($jenis === $cat['jenis']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['jenis']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-filter"></i> Filter</button>
        <?php if (!empty($search) || !empty($jenis)): ?>
          <a href="dashboard.php" class="btn btn-light border" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Card Grid Lapangan -->
  <div class="row g-4 mb-5">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): 
        $foto = !empty($row['foto']) ? $row['foto'] : 'lapangan_sintetis.jpg';
      ?>
        <div class="col-lg-4 col-md-6">
          <div class="card-lapangan">
            <div class="card-lapangan-img-wrapper">
              <span class="badge-jenis"><i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($row['jenis']) ?></span>
              <img src="../uploads/foto_lapangan/<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($row['nama_lapangan']) ?>" onerror="this.src='../uploads/foto_lapangan/lapangan_sintetis.jpg'">
            </div>
            <div class="card-lapangan-body">
              <h4 class="card-lapangan-title"><?= htmlspecialchars($row['nama_lapangan']) ?></h4>
              <p class="card-lapangan-desc">
                <?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : 'Lapangan futsal kualitas internasional dengan pencahayaan terang dan fasilitas terlengkap.' ?>
              </p>
              <div class="card-lapangan-footer">
                <div>
                  <div class="harga-tag" style="color:#881337;"><?= format_rupiah($row['harga_per_jam']) ?></div>
                  <div class="harga-unit">per jam</div>
                </div>
                <a href="booking.php?id_lapangan=<?= $row['id_lapangan'] ?>" class="btn btn-primary-custom px-3 py-2 text-white">
                  <i class="bi bi-calendar-check me-1"></i> Pesan Sekarang
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center py-5">
        <div class="p-5 bg-white rounded-4 shadow-sm">
          <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
          <h5 class="fw-bold text-dark">Lapangan Tidak Ditemukan</h5>
          <p class="text-secondary small mb-3">Tidak ada hasil yang cocok dengan kata kunci atau filter yang Anda masukkan.</p>
          <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Reset Pencarian</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
