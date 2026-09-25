<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('admin/dashboard.php'));
    exit;
}

$id_lapangan = clean($_GET['id_lapangan'] ?? '');
if (empty($id_lapangan)) {
    header("Location: dashboard.php");
    exit;
}

$query_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan='$id_lapangan'");
$lapangan = mysqli_fetch_assoc($query_lap);

if (!$lapangan) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $tanggal     = clean($_POST['tanggal']);
    $jam_mulai   = clean($_POST['jam_mulai']);
    $jam_selesai = clean($_POST['jam_selesai']);
    $id_user     = $_SESSION['id_user'];

    // Validasi waktu
    $time_start = strtotime("$tanggal $jam_mulai");
    $time_end   = strtotime("$tanggal $jam_selesai");

    if ($time_end <= $time_start) {
        $error = "Jam selesai harus lebih akhir dari jam mulai!";
    } elseif ($tanggal < date('Y-m-d')) {
        $error = "Tanggal booking tidak boleh tanggal yang sudah lewat!";
    } else {
        // Hitung durasi jam
        $duration_seconds = $time_end - $time_start;
        $hours = $duration_seconds / 3600;
        $total_harga = ceil($hours * $lapangan['harga_per_jam']);

        // Cek Bentrok Jadwal (Overlap Check)
        $check_conflict = mysqli_query($conn, "
            SELECT * FROM booking 
            WHERE id_lapangan = '$id_lapangan' 
              AND tanggal = '$tanggal' 
              AND status IN ('menunggu', 'disetujui', 'lunas')
              AND (jam_mulai < '$jam_selesai' AND jam_selesai > '$jam_mulai')
        ");

        if (mysqli_num_rows($check_conflict) > 0) {
            $conflict = mysqli_fetch_assoc($check_conflict);
            $error = "Maaf, lapangan ini sudah dibooking pada jam " . substr($conflict['jam_mulai'], 0, 5) . " - " . substr($conflict['jam_selesai'], 0, 5) . " WIB di tanggal tersebut!";
        } else {
            $sql = "INSERT INTO booking (id_user, id_lapangan, tanggal, jam_mulai, jam_selesai, total_harga, status)
                    VALUES ('$id_user', '$id_lapangan', '$tanggal', '$jam_mulai', '$jam_selesai', '$total_harga', 'menunggu')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['booking_success'] = "Booking berhasil diajukan! Menunggu persetujuan admin.";
                header("Location: history.php");
                exit;
            } else {
                $error = "Gagal memproses booking: " . mysqli_error($conn);
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      
      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm mb-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
      </a>

      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="row g-0">
          <!-- Sidebar Info Lapangan -->
          <div class="col-md-5 text-white p-4 p-md-5 d-flex flex-column justify-content-between position-relative" style="background: linear-gradient(135deg, #1e050c 0%, #111827 100%);">
            <div>
              <span class="badge bg-danger bg-opacity-25 text-white border border-danger border-opacity-50 px-3 py-2 rounded-pill mb-3">
                <i class="bi bi-geo-alt-fill me-1"></i> Arena Olahraga
              </span>
              <h3 class="fw-bold mb-2"><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h3>
              <p class="badge bg-light text-dark mb-3"><?= htmlspecialchars($lapangan['jenis']) ?></p>
              
              <div class="my-3">
                <img src="../uploads/foto_lapangan/<?= htmlspecialchars($lapangan['foto'] ?: 'lapangan_sintetis.jpg') ?>" alt="Lapangan" class="img-fluid rounded-3 shadow border border-secondary border-opacity-25" style="max-height: 200px; width: 100%; object-fit: cover;">
              </div>

              <p class="small text-secondary mb-4">
                <?= htmlspecialchars($lapangan['deskripsi'] ?: 'Fasilitas berkualitas tinggi dengan penerangan LED terang dan ruang ganti pemain.') ?>
              </p>
            </div>

            <div class="pt-3 border-top border-white border-opacity-10">
              <div class="small text-secondary">Harga Sewa:</div>
              <div class="fs-3 fw-bold" style="color:#fb7185;"><?= format_rupiah($lapangan['harga_per_jam']) ?> <span class="fs-6 fw-normal text-secondary">/ jam</span></div>
            </div>
          </div>

          <!-- Form Booking -->
          <div class="col-md-7 p-4 p-md-5 bg-white">
            <h4 class="fw-bold text-dark mb-1">Formulir Booking</h4>
            <p class="text-secondary small mb-4">Isi tanggal dan durasi jam main Anda dengan teliti.</p>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?= htmlspecialchars($error) ?></div>
              </div>
            <?php endif; ?>

            <form method="post" action="" id="bookingForm">
              <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Pilih Tanggal Main *</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control form-control-custom" min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>">
              </div>

              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="form-label fw-semibold text-dark small">Jam Mulai *</label>
                  <input type="time" name="jam_mulai" id="jam_mulai" class="form-control form-control-custom" required value="<?= htmlspecialchars($_POST['jam_mulai'] ?? '15:00') ?>">
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold text-dark small">Jam Selesai *</label>
                  <input type="time" name="jam_selesai" id="jam_selesai" class="form-control form-control-custom" required value="<?= htmlspecialchars($_POST['jam_selesai'] ?? '17:00') ?>">
                </div>
              </div>

              <!-- Estimasi Harga Card -->
              <div class="p-3 bg-light rounded-3 mb-4 border">
                <div class="d-flex justify-content-between align-items-center small text-secondary mb-1">
                  <span>Estimasi Durasi:</span>
                  <span id="durasiText" class="fw-bold text-dark">2 Jam</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-semibold text-dark">Total Estimasi Biaya:</span>
                  <span id="totalHargaText" class="fs-5 fw-bold" style="color:#be123c;"><?= format_rupiah($lapangan['harga_per_jam'] * 2) ?></span>
                </div>
              </div>

              <button type="submit" name="submit" class="btn btn-primary-custom w-100 py-3">
                <i class="bi bi-calendar-check-fill me-2"></i>Konfirmasi & Pesan Lapangan
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const hargaPerJam = <?= (int)$lapangan['harga_per_jam'] ?>;
    const jamMulaiInput = document.getElementById('jam_mulai');
    const jamSelesaiInput = document.getElementById('jam_selesai');
    const durasiText = document.getElementById('durasiText');
    const totalHargaText = document.getElementById('totalHargaText');

    function hitungEstimasi() {
        const startVal = jamMulaiInput.value;
        const endVal = jamSelesaiInput.value;

        if (startVal && endVal) {
            const startParts = startVal.split(':');
            const endParts = endVal.split(':');

            const startMinutes = parseInt(startParts[0]) * 60 + parseInt(startParts[1]);
            const endMinutes = parseInt(endParts[0]) * 60 + parseInt(endParts[1]);

            let diffMinutes = endMinutes - startMinutes;
            if (diffMinutes > 0) {
                const hours = diffMinutes / 60;
                const total = Math.ceil(hours * hargaPerJam);
                durasiText.innerText = hours.toFixed(1) + " Jam";
                totalHargaText.innerText = "Rp " + total.toLocaleString('id-ID');
            } else {
                durasiText.innerText = "Waktu Tidak Valid";
                totalHargaText.innerText = "Rp 0";
            }
        }
    }

    jamMulaiInput.addEventListener('change', hitungEstimasi);
    jamSelesaiInput.addEventListener('change', hitungEstimasi);
    hitungEstimasi();
});
</script>

<?php include '../includes/footer.php'; ?>
