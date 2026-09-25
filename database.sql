-- Database: `db_futsal`
-- Export database lengkap untuk Sistem Sewa Lapangan Futsal (FutsalKu)

CREATE DATABASE IF NOT EXISTS `db_futsal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_futsal`;

-- --------------------------------------------------------
-- Struktur Tabel `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Struktur Tabel `lapangan`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lapangan` (
  `id_lapangan` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_lapangan` VARCHAR(100) NOT NULL,
  `jenis` VARCHAR(50) NOT NULL,
  `harga_per_jam` INT(11) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`id_lapangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Struktur Tabel `booking`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `booking` (
  `id_booking` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `id_lapangan` INT(11) NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `total_harga` INT(11) NOT NULL DEFAULT 0,
  `status` ENUM('menunggu', 'disetujui', 'lunas', 'ditolak', 'batal') NOT NULL DEFAULT 'menunggu',
  `bukti_pembayaran` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_booking`),
  KEY `id_user` (`id_user`),
  KEY `id_lapangan` (`id_lapangan`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_lapangan` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Data Seed (Default Users & Lapangan)
-- --------------------------------------------------------

-- Password default admin & user: password123 (MD5 & bcrypt supported)
INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `email`, `no_hp`, `role`) VALUES
(1, 'admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1z5f8.lM3E5C3m0051S3W.5q1d3r/6W', 'Administrator FutsalKu', 'admin@futsalku.com', '081234567890', 'admin'),
(2, 'user1', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1z5f8.lM3E5C3m0051S3W.5q1d3r/6W', 'Budi Santoso', 'budi@gmail.com', '089876543210', 'user')
ON DUPLICATE KEY UPDATE `username`=`username`;

INSERT INTO `lapangan` (`id_lapangan`, `nama_lapangan`, `jenis`, `harga_per_jam`, `deskripsi`, `foto`) VALUES
(1, 'Lapangan Vinyl A', 'Futsal Vinyl', 150000, 'Lapangan futsal rumput vinyl standar internasional dengan pencahayaan LED terang.', 'lapangan_vinyl.jpg'),
(2, 'Lapangan Rumput Sintetis B', 'Futsal Sintetis', 180000, 'Rumput sintetis tebal berkualitas tinggi dengan shockpad nyaman untuk pergelangan kaki.', 'lapangan_sintetis.jpg'),
(3, 'Lapangan Interlock C', 'Futsal Interlock', 160000, 'Lantai interlock matras anti slip, sangat cocok untuk kompetisi cepat.', 'lapangan_interlock.jpg'),
(4, 'Lapangan Badminton Ultra', 'Badminton Vinyl', 80000, 'Lapangan bulutangkis karpet elastis berstandar BWF.', 'lapangan_badminton.jpg')
ON DUPLICATE KEY UPDATE `nama_lapangan`=`nama_lapangan`;
