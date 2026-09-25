<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$search = clean($_GET['search'] ?? '');
$where_sql = "WHERE role='user'";
if (!empty($search)) {
    $where_sql .= " AND (username LIKE '%$search%' OR nama LIKE '%$search%' OR email LIKE '%$search%')";
}

$result = mysqli_query($conn, "SELECT * FROM users $where_sql ORDER BY id_user DESC");

include '../includes/admin_header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h3 class="fw-bold text-dark mb-1">Kelola Data Pelanggan</h3>
    <p class="text-secondary small mb-0">Daftar pengguna terdaftar pada platform FutsalKu.</p>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
  <form method="get" action="" class="row g-2">
    <div class="col-md-9">
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari berdasarkan nama, username, atau email..." value="<?= htmlspecialchars($search) ?>">
      </div>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-filter"></i> Cari User</button>
      <?php if (!empty($search)): ?>
        <a href="kelola_user.php" class="btn btn-light border" title="Reset Search"><i class="bi bi-x-lg"></i></a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="custom-table-card shadow-sm">
  <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-2" style="color:#be123c;"></i> Daftar Akun Pelanggan</h6>
    <span class="badge bg-danger bg-opacity-10 text-maroon border border-danger border-opacity-25 px-3 py-1 rounded-pill" style="color:#be123c !important;">
      Total: <?= mysqli_num_rows($result) ?> Pelanggan
    </span>
  </div>
  <div class="table-responsive">
    <table class="table custom-table align-middle">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama & Email</th>
          <th>Username</th>
          <th>No. WhatsApp / HP</th>
          <th>Tanggal Terdaftar</th>
          <th class="text-end">Aksi Pengelolaan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php $no = 1; while($row = mysqli_fetch_assoc($result)): 
            $raw_hp = !empty($row['no_hp']) ? preg_replace('/[^0-9]/', '', $row['no_hp']) : '';
            $wa_link = '';
            if (!empty($raw_hp)) {
                if (str_starts_with($raw_hp, '0')) {
                    $raw_hp = '62' . substr($raw_hp, 1);
                }
                $wa_link = "https://wa.me/" . $raw_hp;
            }
            $tgl_daftar = !empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : '-';
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['nama']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
              </td>
              <td><span class="badge bg-light text-dark border px-3 py-1 rounded-pill">@<?= htmlspecialchars($row['username']) ?></span></td>
              <td>
                <?php if (!empty($wa_link)): ?>
                  <a href="<?= $wa_link ?>" target="_blank" class="btn btn-sm btn-outline-success border-0 rounded-pill px-2 py-1" title="Chat via WhatsApp">
                    <i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($row['no_hp']) ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted small"><?= htmlspecialchars($row['no_hp'] ?? '-') ?></span>
                <?php endif; ?>
              </td>
              <td>
                <div class="small text-secondary"><?= $tgl_daftar ?></div>
              </td>
              <td class="text-end">
                <a href="hapus_user.php?id=<?= $row['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus akun pelanggan ini beserta riwayatnya?')" class="btn btn-sm btn-outline-danger px-3">
                  <i class="bi bi-trash me-1"></i> Hapus User
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">Data pelanggan tidak ditemukan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
