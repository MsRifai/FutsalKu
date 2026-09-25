<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id_lapangan DESC");

include '../includes/admin_header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h3 class="fw-bold text-dark mb-1">Kelola Data Lapangan</h3>
    <p class="text-secondary small mb-0">Tambah, ubah, atau hapus lapangan futsal & fasilitas olahraga.</p>
  </div>
  <div class="mt-3 mt-md-0">
    <a href="tambah_lapangan.php" class="btn btn-primary-custom px-4">
      <i class="bi bi-plus-circle me-1"></i> Tambah Lapangan Baru
    </a>
  </div>
</div>

<div class="custom-table-card shadow-sm">
  <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap-fill me-2" style="color:#be123c;"></i> Daftar Lapangan Aktif</h6>
    <span class="badge bg-danger bg-opacity-10 text-maroon border border-danger border-opacity-25 px-3 py-1 rounded-pill" style="color:#be123c !important;">
      Total: <?= mysqli_num_rows($result) ?> Lapangan
    </span>
  </div>
  <div class="table-responsive">
    <table class="table custom-table align-middle">
      <thead>
        <tr>
          <th>No</th>
          <th>Foto Arena</th>
          <th>Nama Lapangan</th>
          <th>Jenis Matras / Lantai</th>
          <th>Harga Sewa / Jam</th>
          <th class="text-end">Aksi Kelola</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php $no = 1; while($row = mysqli_fetch_assoc($result)): 
            $foto = !empty($row['foto']) ? $row['foto'] : 'lapangan_sintetis.jpg';
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <img src="../uploads/foto_lapangan/<?= htmlspecialchars($foto) ?>" alt="Foto" class="rounded-3 shadow-sm border" style="width: 80px; height: 55px; object-fit: cover;" onerror="this.src='../uploads/foto_lapangan/lapangan_sintetis.jpg'">
              </td>
              <td>
                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['nama_lapangan']) ?></div>
                <div class="small text-muted text-truncate" style="max-width: 280px;"><?= htmlspecialchars($row['deskripsi'] ?? 'Fasilitas standar internasional') ?></div>
              </td>
              <td>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?= htmlspecialchars($row['jenis']) ?></span>
              </td>
              <td>
                <span class="fw-bold fs-6" style="color:#be123c;"><?= format_rupiah($row['harga_per_jam']) ?></span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                  <a href="edit_lapangan.php?id=<?= $row['id_lapangan'] ?>" class="btn btn-sm btn-warning text-white px-3" title="Edit Lapangan">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </a>
                  <a href="hapus_lapangan.php?id=<?= $row['id_lapangan'] ?>" onclick="return confirm('Yakin ingin menghapus lapangan ini? Semua data terkait akan terhapus.')" class="btn btn-sm btn-outline-danger px-3" title="Hapus Lapangan">
                    <i class="bi bi-trash me-1"></i> Hapus
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">Belum ada data lapangan. Klik Tambah Lapangan Baru.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
