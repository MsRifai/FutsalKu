<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_futsal";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal ke database: " . mysqli_connect_error());
}

// Set timezone Indonesia WIB
date_default_timezone_set('Asia/Jakarta');

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto Schema Migration Helper - Menjamin kolom baru otomatis ditambahkan ke DB tanpa merusak data lama
function auto_migrate_db() {
    global $conn;
    
    // Cek dan tambah no_hp di tabel users
    $chk_nohp = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'no_hp'");
    if ($chk_nohp && mysqli_num_rows($chk_nohp) == 0) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `no_hp` VARCHAR(20) DEFAULT NULL");
    }

    // Cek dan tambah created_at di tabel users
    $chk_u_created = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'created_at'");
    if ($chk_u_created && mysqli_num_rows($chk_u_created) == 0) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    // Cek dan tambah deskripsi di tabel lapangan
    $chk_desc = mysqli_query($conn, "SHOW COLUMNS FROM `lapangan` LIKE 'deskripsi'");
    if ($chk_desc && mysqli_num_rows($chk_desc) == 0) {
        @mysqli_query($conn, "ALTER TABLE `lapangan` ADD COLUMN `deskripsi` TEXT DEFAULT NULL");
    }

    // Cek dan tambah total_harga di tabel booking
    $chk_harga = mysqli_query($conn, "SHOW COLUMNS FROM `booking` LIKE 'total_harga'");
    if ($chk_harga && mysqli_num_rows($chk_harga) == 0) {
        @mysqli_query($conn, "ALTER TABLE `booking` ADD COLUMN `total_harga` INT(11) NOT NULL DEFAULT 0");
    }

    // Cek dan tambah created_at di tabel booking
    $chk_b_created = mysqli_query($conn, "SHOW COLUMNS FROM `booking` LIKE 'created_at'");
    if ($chk_b_created && mysqli_num_rows($chk_b_created) == 0) {
        @mysqli_query($conn, "ALTER TABLE `booking` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
}

// Jalankan auto migrasi
auto_migrate_db();

// Base URL Helper
if (!function_exists('base_url')) {
    function base_url($path = '') {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . "://" . $host . "/sewalapangan/" . ltrim($path, '/');
    }
}

// Sanitize input string
if (!function_exists('clean')) {
    function clean($data) {
        global $conn;
        if (is_array($data)) {
            return array_map('clean', $data);
        }
        return mysqli_real_escape_string($conn, trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8')));
    }
}

// Format Rupiah
if (!function_exists('format_rupiah')) {
    function format_rupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ',', '.');
    }
}

// Format Tanggal Indo
if (!function_exists('format_tanggal')) {
    function format_tanggal($date) {
        if (!$date || $date == '0000-00-00') return '-';
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $split = explode('-', $date);
        if (count($split) === 3) {
            return (int)$split[2] . ' ' . ($bulan[(int)$split[1]] ?? '') . ' ' . $split[0];
        }
        return $date;
    }
}
?>
