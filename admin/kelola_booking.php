<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$status_filter = clean($_GET['status'] ?? '');

$where_sql = "";
if (!empty($status_filter)) {
    $where_sql = "WHERE b.status = '$status_filter'";
}

$query = "
    SELECT b.*, u.nama as nama_user, u.email as email_user, u.username, u.no_hp,
           l.nama_lapangan, l.harga_per_jam 
    FROM booking b 
    JOIN users u ON b.id_user = u.id_user 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    $where_sql 
    ORDER BY b.id_booking DESC
";
$result = mysqli_query($conn, $query);

// Count stats for filter pills
$cnt_all      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM booking"))['cnt'] ?? 0;
$cnt_pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM booking WHERE status='menunggu'"))['cnt'] ?? 0;
$cnt_approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM booking WHERE status='disetujui'"))['cnt'] ?? 0;
$cnt_lunas    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM booking WHERE status='lunas'"))['cnt'] ?? 0;
$cnt_ditolak  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM booking WHERE status='ditolak'"))['cnt'] ?? 0;

include '../includes/admin_header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h3 class="fw-bold text-dark mb-1">Kelola Data Transaksi Booking</h3>
    <p class="text-secondary small mb-0">Verifikasi permohonan booking dan verifikasi bukti pembayaran transfer pelanggan.</p>
  </div>
</div>

<!-- Filter Tabs Maroon -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="kelola_booking.php" class="btn btn-sm <?= empty($status_filter) ? 'btn-dark' : 'btn-light border' ?> rounded-pill px-3 py-2">
    Semua Booking <span class="badge bg-secondary ms-1"><?= $cnt_all ?></span>
  </a>
  <a href="kelola_booking.php?status=menunggu" class="btn btn-sm <?= ($status_filter == 'menunggu') ? 'btn-warning' : 'btn-light border' ?> rounded-pill px-3 py-2">
    Menunggu Admin <span class="badge bg-dark ms-1"><?= $cnt_pending ?></span>
  </a>
  <a href="kelola_booking.php?status=disetujui" class="btn btn-sm <?= ($status_filter == 'disetujui') ? 'btn-primary' : 'btn-light border' ?> rounded-pill px-3 py-2">
    Disetujui (Bayar) <span class="badge bg-light text-dark ms-1"><?= $cnt_approved ?></span>
  </a>
  <a href="kelola_booking.php?status=lunas" class="btn btn-sm <?= ($status_filter == 'lunas') ? 'text-white' : 'btn-light border' ?> rounded-pill px-3 py-2" style="<?= ($status_filter == 'lunas') ? 'background-color:#be123c;' : '' ?>">
    Lunas <span class="badge bg-light text-dark ms-1"><?= $cnt_lunas ?></span>
  </a>
  <a href="kelola_booking.php?status=ditolak" class="btn btn-sm <?= ($status_filter == 'ditolak') ? 'btn-danger' : 'btn-light border' ?> rounded-pill px-3 py-2">
    Ditolak <span class="badge bg-dark ms-1"><?= $cnt_ditolak ?></span>
  </a>
</div>

<div class="custom-table-card shadow-sm">
  <div class="table-responsive">
    <table class="table custom-table align-middle">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Pelanggan</th>
          <th>Lapangan</th>
          <th>Tanggal & Jam</th>
          <th>Total Harga</th>
          <th>Status</th>
          <th>Bukti Transfer</th>
          <th class="text-end">Aksi Verifikasi</th>
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
            $hp_user = !empty($row['no_hp']) ? $row['no_hp'] : ($row['email_user'] ?? '-');
          ?>
            <tr>
              <td><span class="fw-bold text-dark">#BK-<?= sprintf('%04d', $row['id_booking']) ?></span></td>
              <td>
                <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_user']) ?></div>
                <div class="small text-muted"><i class="bi bi-whatsapp me-1 text-success"></i><?= htmlspecialchars($hp_user) ?></div>
              </td>
              <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
              <td>
                <div class="fw-semibold text-dark"><?= format_tanggal($row['tanggal']) ?></div>
                <div class="small text-muted"><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?> WIB</div>
              </td>
              <td>
                <span class="fw-bold" style="color:#be123c;"><?= format_rupiah($total) ?></span>
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
              <td>
                <?php if (!empty($row['bukti_pembayaran'])): ?>
                  <a href="../uploads/bukti_pembayaran/<?= urlencode($row['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" style="border-color:#be123c; color:#be123c;">
                    <i class="bi bi-file-earmark-image me-1"></i> Lihat Bukti
                  </a>
                <?php else: ?>
                  <span class="text-muted small">-</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                  <?php if ($st == 'menunggu'): ?>
                    <a href="ubah_status_booking.php?id=<?= $row['id_booking'] ?>&status=disetujui" onclick="return confirm('Setujui permohonan booking ini?')" class="btn btn-sm btn-success">
                      <i class="bi bi-check-lg me-1"></i> Setujui
                    </a>
                    <a href="ubah_status_booking.php?id=<?= $row['id_booking'] ?>&status=ditolak" onclick="return confirm('Tolak permohonan booking ini?')" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-x-lg me-1"></i> Tolak
                    </a>
                  <?php elseif ($st == 'disetujui' && !empty($row['bukti_pembayaran'])): ?>
                    <a href="verifikasi_bukti.php?id=<?= $row['id_booking'] ?>&status=lunas" onclick="return confirm('Verifikasi pembayaran sah dan ubah status ke LUNAS?')" class="btn btn-sm text-white" style="background-color:#be123c;">
                      <i class="bi bi-patch-check-fill me-1"></i> Verifikasi Lunas
                    </a>
                    <a href="verifikasi_bukti.php?id=<?= $row['id_booking'] ?>&status=ditolak" onclick="return confirm('Tolak bukti pembayaran ini?')" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-x-circle me-1"></i> Tolak
                    </a>
                  <?php elseif ($st == 'lunas' || $st == 'disetujui'): ?>
                    <a href="../user/invoice.php?id=<?= $row['id_booking'] ?>" target="_blank" class="btn btn-sm btn-light border">
                      <i class="bi bi-printer me-1"></i> Invoice
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">Tidak ada data booking dengan status ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
