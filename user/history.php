<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('admin/dashboard.php'));
    exit;
}

$id_user = $_SESSION['id_user'];
$status_filter = clean($_GET['status'] ?? '');

$where_clause = "WHERE b.id_user='$id_user'";
if (!empty($status_filter)) {
    $where_clause .= " AND b.status='$status_filter'";
}

$query = "
    SELECT b.*, l.nama_lapangan, l.harga_per_jam, l.jenis 
    FROM booking b 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    $where_clause
    ORDER BY b.id_booking DESC
";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container py-4">
  <!-- Toast / Notification Alert Maroon -->
  <?php if (isset($_SESSION['booking_success'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: '<?= htmlspecialchars($_SESSION['booking_success']) ?>',
          confirmColor: '#be123c'
        });
      });
    </script>
    <?php unset($_SESSION['booking_success']); ?>
  <?php endif; ?>

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h3 class="fw-bold text-dark mb-1">Riwayat Booking Saya</h3>
      <p class="text-secondary small mb-0">Pantau status persetujuan, upload bukti transfer, dan cetak invoice pembayaran.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="dashboard.php" class="btn btn-primary-custom px-4">
        <i class="bi bi-plus-lg me-1"></i> Booking Lapangan Lagi
      </a>
    </div>
  </div>

  <!-- Filter Status Tabs Maroon -->
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="history.php" class="btn btn-sm <?= empty($status_filter) ? 'btn-dark' : 'btn-light border' ?> rounded-pill px-3 py-2">Semua</a>
    <a href="history.php?status=menunggu" class="btn btn-sm <?= ($status_filter == 'menunggu') ? 'btn-warning' : 'btn-light border' ?> rounded-pill px-3 py-2">Menunggu</a>
    <a href="history.php?status=disetujui" class="btn btn-sm <?= ($status_filter == 'disetujui') ? 'btn-primary' : 'btn-light border' ?> rounded-pill px-3 py-2">Disetujui</a>
    <a href="history.php?status=lunas" class="btn btn-sm <?= ($status_filter == 'lunas') ? 'text-white' : 'btn-light border' ?> rounded-pill px-3 py-2" style="<?= ($status_filter == 'lunas') ? 'background-color:#be123c;' : '' ?>">Lunas</a>
    <a href="history.php?status=ditolak" class="btn btn-sm <?= ($status_filter == 'ditolak') ? 'btn-danger' : 'btn-light border' ?> rounded-pill px-3 py-2">Ditolak</a>
  </div>

  <!-- Table Card -->
  <div class="custom-table-card">
    <div class="table-responsive">
      <table class="table custom-table align-middle">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Lapangan</th>
            <th>Tanggal Main</th>
            <th>Jam / Durasi</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Bukti Pembayaran</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
              $total = $row['total_harga'] ?? 0;
              if ($total == 0) {
                $hours = (strtotime($row['jam_selesai']) - strtotime($row['jam_mulai'])) / 3600;
                $total = ceil($hours * $row['harga_per_jam']);
              }
              $st = $row['status'];
            ?>
              <tr>
                <td><span class="fw-bold text-dark">#BK-<?= sprintf('%04d', $row['id_booking']) ?></span></td>
                <td>
                  <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lapangan']) ?></div>
                  <div class="small text-secondary"><?= htmlspecialchars($row['jenis']) ?></div>
                </td>
                <td>
                  <div class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1 text-secondary"></i><?= format_tanggal($row['tanggal']) ?></div>
                </td>
                <td>
                  <div class="small text-dark fw-semibold"><i class="bi bi-clock me-1 text-secondary"></i><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?> WIB</div>
                </td>
                <td>
                  <span class="fw-bold" style="color:#be123c;"><?= format_rupiah($total) ?></span>
                </td>
                <td>
                  <?php if ($st == 'menunggu'): ?>
                    <span class="badge-status badge-status-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu Admin</span>
                  <?php elseif ($st == 'disetujui'): ?>
                    <span class="badge-status badge-status-disetujui"><i class="bi bi-check-circle"></i> Disetujui (Bayar)</span>
                  <?php elseif ($st == 'lunas'): ?>
                    <span class="badge-status badge-status-lunas"><i class="bi bi-patch-check-fill"></i> Lunas</span>
                  <?php elseif ($st == 'ditolak'): ?>
                    <span class="badge-status badge-status-ditolak"><i class="bi bi-x-circle"></i> Ditolak</span>
                  <?php elseif ($st == 'batal'): ?>
                    <span class="badge-status badge-status-batal"><i class="bi bi-slash-circle"></i> Dibatalkan</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($row['bukti_pembayaran'])): ?>
                    <a href="../uploads/bukti_pembayaran/<?= urlencode($row['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-file-earmark-image me-1"></i> Lihat Bukti
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-1">
                    <?php if ($st == 'disetujui' && empty($row['bukti_pembayaran'])): ?>
                      <a href="upload_bukti.php?id=<?= $row['id_booking'] ?>" class="btn btn-sm text-white px-3" style="background-color:#be123c;">
                        <i class="bi bi-upload me-1"></i> Upload Bukti
                      </a>
                    <?php endif; ?>

                    <?php if ($st == 'lunas' || $st == 'disetujui'): ?>
                      <a href="invoice.php?id=<?= $row['id_booking'] ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Cetak Struk/Invoice">
                        <i class="bi bi-printer-fill me-1"></i> Invoice
                      </a>
                    <?php endif; ?>

                    <?php if ($st == 'menunggu'): ?>
                      <a href="batal_booking.php?id=<?= $row['id_booking'] ?>" onclick="return confirm('Yakin ingin membatalkan booking ini?')" class="btn btn-sm btn-outline-secondary">
                        Batalkan
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                <div class="text-muted">Belum ada data riwayat booking.</div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
