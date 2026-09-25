<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('admin/dashboard.php'));
    exit;
}

$id_booking = clean($_GET['id'] ?? '');
$id_user = $_SESSION['id_user'];

$query = mysqli_query($conn, "
    SELECT b.*, l.nama_lapangan, l.harga_per_jam 
    FROM booking b 
    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
    WHERE b.id_booking='$id_booking' AND b.id_user='$id_user'
");
$booking = mysqli_fetch_assoc($query);

if (!$booking) {
    header("Location: history.php");
    exit;
}

if ($booking['status'] === 'lunas') {
    header("Location: history.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $file_name = $_FILES['bukti']['name'];
        $tmp_name  = $_FILES['bukti']['tmp_name'];
        $file_size = $_FILES['bukti']['size'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
        if (!in_array($ext, $allowed_ext)) {
            $error = "Format file tidak didukung! Format yang diperbolehkan: JPG, PNG, WEBP, PDF.";
        } elseif ($file_size > 5 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar! Maksimal 5 MB.";
        } else {
            $folder = "../uploads/bukti_pembayaran/";
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $new_name = 'bukti_' . $id_booking . '_' . time() . '.' . $ext;
            if (move_uploaded_file($tmp_name, $folder . $new_name)) {
                mysqli_query($conn, "UPDATE booking SET bukti_pembayaran='$new_name' WHERE id_booking='$id_booking'");
                $_SESSION['booking_success'] = "Bukti pembayaran berhasil diunggah! Menunggu konfirmasi admin.";
                header("Location: history.php");
                exit;
            } else {
                $error = "Gagal mengunggah berkas ke server.";
            }
        }
    } else {
        $error = "Silakan pilih berkas bukti pembayaran terlebih dahulu!";
    }
}

// Hitung total harga
$total = $booking['total_harga'] ?? 0;
if ($total == 0) {
    $hours = (strtotime($booking['jam_selesai']) - strtotime($booking['jam_mulai'])) / 3600;
    $total = ceil($hours * $booking['harga_per_jam']);
}

include '../includes/header.php';
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      <a href="history.php" class="btn btn-outline-secondary btn-sm mb-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
      </a>

      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="text-center mb-4">
          <div class="bg-danger bg-opacity-10 text-maroon d-inline-flex p-3 rounded-circle mb-3">
            <i class="bi bi-credit-card-2-front-fill fs-1" style="color:#be123c;"></i>
          </div>
          <h3 class="fw-bold text-dark mb-1">Upload Bukti Pembayaran</h3>
          <p class="text-secondary small mb-0">Kode Booking: <span class="fw-bold text-dark">#BK-<?= sprintf('%04d', $booking['id_booking']) ?></span></p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($error) ?></div>
          </div>
        <?php endif; ?>

        <!-- Detail Pembayaran Box -->
        <div class="p-3 rounded-3 bg-light border mb-4">
          <div class="d-flex justify-content-between mb-2 small text-secondary">
            <span>Lapangan:</span>
            <span class="fw-bold text-dark"><?= htmlspecialchars($booking['nama_lapangan']) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2 small text-secondary">
            <span>Tanggal & Jam:</span>
            <span class="fw-semibold text-dark"><?= format_tanggal($booking['tanggal']) ?> (<?= substr($booking['jam_mulai'],0,5) ?> - <?= substr($booking['jam_selesai'],0,5) ?> WIB)</span>
          </div>
          <div class="d-flex justify-content-between pt-2 border-top">
            <span class="fw-bold text-dark">Total Yang Harus Dibayar:</span>
            <span class="fs-4 fw-bold" style="color:#be123c;"><?= format_rupiah($total) ?></span>
          </div>
        </div>

        <!-- Info Rekening Bank -->
        <div class="card border border-danger border-opacity-25 bg-danger bg-opacity-10 rounded-3 p-3 mb-4">
          <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bank me-2" style="color:#be123c;"></i> Rekening Pembayaran Resmi:</h6>
          <div class="row g-2 small">
            <div class="col-md-6">
              <div class="bg-white p-2 rounded border">
                <div class="text-secondary">Bank BCA</div>
                <div class="fw-bold text-dark fs-6">123-456-7890</div>
                <div class="text-muted fs-7">a.n. PT FutsalKu Indonesia</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="bg-white p-2 rounded border">
                <div class="text-secondary">Bank Mandiri</div>
                <div class="fw-bold text-dark fs-6">987-000-12345</div>
                <div class="text-muted fs-7">a.n. PT FutsalKu Indonesia</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Upload -->
        <form method="post" enctype="multipart/form-data">
          <div class="mb-4">
            <label class="form-label fw-semibold text-dark small">Pilih File Foto / PDF Bukti Transfer *</label>
            <input type="file" name="bukti" class="form-control form-control-custom" accept="image/*,application/pdf" required>
            <div class="form-text fs-7 text-muted">Format: JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.</div>
          </div>

          <button type="submit" name="submit" class="btn btn-primary-custom w-100 py-3">
            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Unggah Bukti Sekarang
          </button>
        </form>

      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>