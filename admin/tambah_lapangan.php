<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$error = '';

if (isset($_POST['submit'])) {
    $nama      = clean($_POST['nama']);
    $jenis     = clean($_POST['jenis']);
    $harga     = clean($_POST['harga']);
    $deskripsi = clean($_POST['deskripsi']);

    if (empty($nama) || empty($jenis) || empty($harga)) {
        $error = "Nama, Jenis, dan Harga per jam wajib diisi!";
    } else {
        $foto_name = 'lapangan_sintetis.jpg';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $raw_name  = $_FILES['foto']['name'];
            $tmp_name  = $_FILES['foto']['tmp_name'];
            $ext       = strtolower(pathinfo($raw_name, PATHINFO_EXTENSION));
            $folder    = "../uploads/foto_lapangan/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $foto_name = 'lapangan_' . time() . '_' . rand(100, 999) . '.' . $ext;
            move_uploaded_file($tmp_name, $folder . $foto_name);
        }

        $sql = "INSERT INTO lapangan (nama_lapangan, jenis, harga_per_jam, deskripsi, foto)
                VALUES ('$nama', '$jenis', '$harga', '$deskripsi', '$foto_name')";

        if (mysqli_query($conn, $sql)) {
            header("Location: kelola_lapangan.php");
            exit;
        } else {
            $error = "Gagal menambah lapangan: " . mysqli_error($conn);
        }
    }
}

include '../includes/admin_header.php';
?>

<div class="row justify-content-center py-2">
  <div class="col-lg-8">
    
    <a href="kelola_lapangan.php" class="btn btn-outline-secondary btn-sm mb-4">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Kelola Lapangan
    </a>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="p-3 text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px; background-color:#be123c;">
          <i class="bi bi-plus-circle fs-4"></i>
        </div>
        <div>
          <h4 class="fw-bold text-dark mb-0">Tambah Lapangan Baru</h4>
          <p class="text-secondary small mb-0">Lengkapi spesifikasi dan unggah foto lapangan.</p>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 small py-2 px-3 mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          <div><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Nama Lapangan *</label>
          <input type="text" name="nama" class="form-control form-control-custom" placeholder="Contoh: Lapangan Vinyl A" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark small">Jenis Matras / Lantai *</label>
            <input type="text" name="jenis" class="form-control form-control-custom" placeholder="Contoh: Futsal Vinyl / Rumput Sintetis" required value="<?= htmlspecialchars($_POST['jenis'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark small">Harga Sewa per Jam (Rp) *</label>
            <input type="number" name="harga" class="form-control form-control-custom" placeholder="150000" required value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Deskripsi & Fasilitas</label>
          <textarea name="deskripsi" class="form-control form-control-custom" rows="3" placeholder="Fasilitas lapangan, tipe jaring, dll."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-dark small">Foto Lapangan</label>
          <input type="file" name="foto" class="form-control form-control-custom" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary-custom w-100 py-3">
          <i class="bi bi-save me-2"></i>Simpan Lapangan Baru
        </button>
      </form>
    </div>

  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>