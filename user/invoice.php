<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['login'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_booking = clean($_GET['id'] ?? '');

$query = mysqli_query($conn, "
    SELECT b.*, l.nama_lapangan, l.jenis, l.harga_per_jam, u.nama, u.username, u.email, u.no_hp
    FROM booking b 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    JOIN users u ON b.id_user = u.id_user 
    WHERE b.id_booking = '$id_booking'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Struk/Invoice tidak ditemukan.";
    exit;
}

// Jika role user, pastikan miliknya sendiri
if ($_SESSION['role'] === 'user' && $data['id_user'] != $_SESSION['id_user']) {
    echo "Akses ditolak!";
    exit;
}

$total = $data['total_harga'] ?? 0;
if ($total == 0) {
    $hours = (strtotime($data['jam_selesai']) - strtotime($data['jam_mulai'])) / 3600;
    $total = ceil($hours * $data['harga_per_jam']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice #BK-<?= sprintf('%04d', $data['id_booking']) ?> - FutsalKu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: #f8fafc; font-family: sans-serif; color: #1e293b; }
    .invoice-card { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 10px 30px rgba(190, 18, 60, 0.08); max-width: 750px; margin: 40px auto; }
    @media print {
      body { background: #fff; }
      .invoice-card { box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="invoice-card border">
    
    <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
      <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</button>
      <button onclick="window.print()" class="btn text-white btn-sm" style="background-color:#be123c;"><i class="bi bi-printer-fill"></i> Cetak Struk / Invoice</button>
    </div>

    <!-- Header Invoice -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-1" style="color:#be123c;"><i class="bi bi-futbol me-2"></i>FutsalKu</h2>
        <div class="text-muted small">Arena Olahraga & Sewa Lapangan Futsal</div>
      </div>
      <div class="text-end">
        <h4 class="fw-bold mb-1">INVOICE</h4>
        <div class="fw-bold text-secondary">#BK-<?= sprintf('%04d', $data['id_booking']) ?></div>
        <div class="small text-muted">Tanggal: <?= date('d/m/Y', strtotime($data['created_at'] ?? 'now')) ?></div>
      </div>
    </div>

    <hr>

    <!-- Info Pemesan & Status -->
    <div class="row my-4">
      <div class="col-6">
        <div class="text-uppercase text-muted fs-7 fw-bold mb-1" style="font-size: 0.75rem;">Diterbitkan Untuk:</div>
        <div class="fw-bold fs-6"><?= htmlspecialchars($data['nama']) ?></div>
        <div class="text-secondary small">Email: <?= htmlspecialchars($data['email']) ?></div>
        <div class="text-secondary small">No. HP: <?= htmlspecialchars($data['no_hp'] ?? '-') ?></div>
      </div>
      <div class="col-6 text-end">
        <div class="text-uppercase text-muted fs-7 fw-bold mb-1" style="font-size: 0.75rem;">Status Pembayaran:</div>
        <?php if ($data['status'] === 'lunas'): ?>
          <span class="badge text-white fs-6 px-3 py-2" style="background-color:#be123c;"><i class="bi bi-patch-check-fill me-1"></i> LUNAS</span>
        <?php elseif ($data['status'] === 'disetujui'): ?>
          <span class="badge bg-primary fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i> DISETUJUI</span>
        <?php else: ?>
          <span class="badge bg-secondary fs-6 px-3 py-2"><?= strtoupper($data['status']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Detail Tabel Rincian -->
    <table class="table table-bordered align-middle my-4">
      <thead class="table-light">
        <tr>
          <th>Deskripsi Sewa</th>
          <th>Tanggal Main</th>
          <th>Jam Main</th>
          <th class="text-end">Harga / Jam</th>
          <th class="text-end">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div class="fw-bold"><?= htmlspecialchars($data['nama_lapangan']) ?></div>
            <div class="small text-muted"><?= htmlspecialchars($data['jenis']) ?></div>
          </td>
          <td><?= format_tanggal($data['tanggal']) ?></td>
          <td><?= substr($data['jam_mulai'],0,5) ?> - <?= substr($data['jam_selesai'],0,5) ?> WIB</td>
          <td class="text-end"><?= format_rupiah($data['harga_per_jam']) ?></td>
          <td class="text-end fw-bold"><?= format_rupiah($total) ?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" class="text-end fw-bold fs-5">Grand Total</td>
          <td class="text-end fw-bold fs-5" style="color:#be123c;"><?= format_rupiah($total) ?></td>
        </tr>
      </tfoot>
    </table>

    <div class="row pt-4 text-secondary small">
      <div class="col-8">
        <p class="mb-1"><strong>Catatan Penting:</strong></p>
        <ul class="ps-3 mb-0">
          <li>Harap datang 10 menit sebelum jam main dimulai.</li>
          <li>Tunjukkan bukti/struk digital ini kepada kasir/petugas lapangan.</li>
          <li>Jaga kebersihan area lapangan dan sepatu wajib sesuai jenis lantai.</li>
        </ul>
      </div>
      <div class="col-4 text-center mt-3 mt-md-0">
        <div class="mb-4">Staf Admin FutsalKu</div>
        <div class="fw-bold text-dark">( Tim Pengelola )</div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
