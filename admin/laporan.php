<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$tgl_mulai   = clean($_GET['tgl_mulai'] ?? date('Y-m-01'));
$tgl_selesai = clean($_GET['tgl_selesai'] ?? date('Y-m-t'));

// Query data laporan berdasarkan rentang tanggal
$query_laporan = mysqli_query($conn, "
    SELECT b.*, u.nama as nama_user, l.nama_lapangan, l.harga_per_jam 
    FROM booking b 
    JOIN users u ON b.id_user = u.id_user 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    WHERE b.tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY b.tanggal ASC
");

// Stat ringkasan
$total_booking = 0;
$total_lunas = 0;
$total_disetujui = 0;
$total_ditolak = 0;
$total_pendapatan = 0;

$data_rows = [];
while($row = mysqli_fetch_assoc($query_laporan)) {
    $data_rows[] = $row;
    $total_booking++;
    $st = $row['status'];

    $harga = $row['total_harga'] ?? 0;
    if ($harga == 0) {
        $hours = (strtotime($row['jam_selesai']) - strtotime($row['jam_mulai'])) / 3600;
        $harga = ceil($hours * $row['harga_per_jam']);
    }

    if ($st == 'lunas') {
        $total_lunas++;
        $total_pendapatan += $harga;
    } elseif ($st == 'disetujui') {
        $total_disetujui++;
    } elseif ($st == 'ditolak') {
        $total_ditolak++;
    }
}

include '../includes/admin_header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h3 class="fw-bold text-dark mb-1">Laporan Keuangan & Rekap Booking</h3>
    <p class="text-secondary small mb-0">Rekapitulasi transaksi pendapatan sewa lapangan per periode.</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button onclick="window.print()" class="btn text-white px-4" style="background-color:#be123c;">
      <i class="bi bi-printer-fill me-1"></i> Cetak Laporan Keuangan
    </button>
  </div>
</div>

<!-- Filter Periode Form Maroon -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
  <form method="get" action="" class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label fw-semibold text-dark small">Dari Tanggal *</label>
      <input type="date" name="tgl_mulai" class="form-control form-control-custom" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold text-dark small">Sampai Tanggal *</label>
      <input type="date" name="tgl_selesai" class="form-control form-control-custom" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
    </div>
    <div class="col-md-4">
      <button type="submit" class="btn btn-primary-custom w-100 py-2">
        <i class="bi bi-filter me-1"></i> Tampilkan Laporan
      </button>
    </div>
  </form>
</div>

<!-- Summary Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card shadow-sm border-0">
      <div class="stat-title">Total Pendapatan (Lunas)</div>
      <div class="stat-value text-maroon" style="color:#be123c !important;"><?= format_rupiah($total_pendapatan) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card shadow-sm border-0">
      <div class="stat-title">Total Permohonan</div>
      <div class="stat-value text-dark"><?= $total_booking ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card shadow-sm border-0">
      <div class="stat-title">Booking Lunas</div>
      <div class="stat-value text-danger" style="color:#be123c !important;"><?= $total_lunas ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card shadow-sm border-0">
      <div class="stat-title">Booking Ditolak</div>
      <div class="stat-value text-muted"><?= $total_ditolak ?></div>
    </div>
  </div>
</div>

<!-- Table Laporan -->
<div class="custom-table-card shadow-sm">
  <div class="p-3 bg-white border-bottom">
    <h6 class="fw-bold text-dark mb-0">Rincian Transaksi (<?= format_tanggal($tgl_mulai) ?> s/d <?= format_tanggal($tgl_selesai) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table custom-table align-middle">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode</th>
          <th>Tanggal Main</th>
          <th>Pelanggan</th>
          <th>Lapangan</th>
          <th>Jam Main</th>
          <th>Status</th>
          <th class="text-end">Nominal</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($data_rows) > 0): ?>
          <?php $no = 1; foreach($data_rows as $row): 
            $total = $row['total_harga'] ?? 0;
            if ($total == 0) {
              $hours = (strtotime($row['jam_selesai']) - strtotime($row['jam_mulai'])) / 3600;
              $total = ceil($hours * $row['harga_per_jam']);
            }
            $st = $row['status'];
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><span class="fw-bold text-dark">#BK-<?= sprintf('%04d', $row['id_booking']) ?></span></td>
              <td><?= format_tanggal($row['tanggal']) ?></td>
              <td><span class="fw-semibold"><?= htmlspecialchars($row['nama_user']) ?></span></td>
              <td><?= htmlspecialchars($row['nama_lapangan']) ?></td>
              <td><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?> WIB</td>
              <td>
                <?php if ($st == 'lunas'): ?>
                  <span class="badge text-white px-3 py-1" style="background-color:#be123c;">Lunas</span>
                <?php elseif ($st == 'disetujui'): ?>
                  <span class="badge bg-primary px-3 py-1">Disetujui</span>
                <?php elseif ($st == 'ditolak'): ?>
                  <span class="badge bg-danger px-3 py-1">Ditolak</span>
                <?php else: ?>
                  <span class="badge bg-secondary px-3 py-1"><?= ucfirst($st) ?></span>
                <?php endif; ?>
              </td>
              <td class="text-end fw-bold text-dark">
                <?= format_rupiah($total) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">Tidak ada transaksi pada periode ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr class="table-light">
          <td colspan="7" class="text-end fw-bold fs-6">TOTAL PENDAPATAN (LUNAS):</td>
          <td class="text-end fw-bold fs-6 text-maroon" style="color:#be123c !important;"><?= format_rupiah($total_pendapatan) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
