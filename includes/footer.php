</div> <!-- end main-wrapper -->

<footer>
  <div class="container py-4">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6">
        <h5 class="text-white fw-bold mb-3"><i class="bi bi-futbol text-emerald me-2"></i>FutsalKu</h5>
        <p class="small text-secondary mb-3">Platform booking lapangan olahraga futsal, badminton, dan mini soccer tercepat, termudah, dan terpercaya di kota Anda.</p>
        <div class="d-flex gap-3 text-white fs-5">
          <a href="#" class="text-secondary"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-secondary"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-secondary"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <h6 class="text-white fw-semibold mb-3">Navigasi</h6>
        <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
          <li><a href="<?= base_url('index.php') ?>">Beranda</a></li>
          <li><a href="<?= base_url('user/dashboard.php') ?>">Daftar Lapangan</a></li>
          <li><a href="<?= base_url('login.php') ?>">Masuk Akun</a></li>
          <li><a href="<?= base_url('register.php') ?>">Pendaftaran</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="text-white fw-semibold mb-3">Layanan & Jenis</h6>
        <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
          <li><a href="#">Futsal Rumput Sintetis</a></li>
          <li><a href="#">Futsal Floor Vinyl</a></li>
          <li><a href="#">Futsal Matras Interlock</a></li>
          <li><a href="#">Lapangan Badminton Karpet</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="text-white fw-semibold mb-3">Hubungi Kami</h6>
        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
          <li><i class="bi bi-geo-alt me-2 text-emerald"></i> Jl. Olahraga No. 88, Jakarta</li>
          <li><i class="bi bi-telephone me-2 text-emerald"></i> (021) 555-8899</li>
          <li><i class="bi bi-whatsapp me-2 text-emerald"></i> +62 812-3456-7890</li>
          <li><i class="bi bi-envelope me-2 text-emerald"></i> info@futsalku.com</li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary opacity-25 my-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center small text-secondary">
      <p class="mb-0">&copy; <?= date('Y') ?> FutsalKu. All rights reserved.</p>
      <p class="mb-0">Sistem Sewa Lapangan Futsal Modern</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
