# FutsalKu - Aplikasi Sewa Lapangan Futsal

Aplikasi web berbasis PHP & MySQL untuk manajemen pemesanan dan penyewaan lapangan futsal secara online.

## 🚀 Fitur Utama
- **User Dashboard**: Registrasi, login, lihat jadwal lapangan, booking lapangan, upload bukti pembayaran, dan riwayat pemesanan.
- **Admin Dashboard**: Manajemen data lapangan, verifikasi pembayaran, kelola jadwal booking, dan laporan transaksi.
- **Auto Schema Migration**: Otomatis memperbarui struktur database tanpa merusak data lama.

---

## 🛠️ Persyaratan Sistem
- Web Server (XAMPP / WAMP / Laragon)
- PHP version 7.4 atau lebih baru
- MySQL / MariaDB Database Server

---

## 📥 Cara Penggunaan / Instalasi untuk Orang Lain

Jika pengguna lain ingin memakai atau menjalankan proyek ini di komputer mereka, ikuti langkah-langkah berikut:

### 1. Clone atau Download Repositori
Jalankan perintah berikut di terminal/command prompt folder web server (contoh: `C:\xampp\htdocs\`):
```bash
git clone https://github.com/MsRifai/FutsalKu.git
```
*Atau klik tombol **Code** -> **Download ZIP** di halaman GitHub ini, lalu ekstrak ke folder `htdocs`.*

### 2. Impor Database
1. Buka browser dan masuk ke **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama **`db_futsal`**.
3. Pilih database `db_futsal`, lalu masuk ke tab **Import**.
4. Pilih file **`database.sql`** yang ada di dalam folder proyek ini, lalu klik **Go / Kirim**.

### 3. Konfigurasi Database (Jika Perlu)
File konfigurasi database berada pada **`config/config.php`**. 
Secara bawaan:
- **Host**: `localhost`
- **User**: `root`
- **Password**: `""` (kosong)
- **Database**: `db_futsal`

Sesuaikan username dan password database di `config/config.php` jika menggunakan konfigurasi MySQL yang berbeda.

### 4. Jalankan Aplikasi
1. Pastikan modul **Apache** dan **MySQL** pada XAMPP sudah diaktifkan (`Start`).
2. Buka browser dan akses URL:
   ```text
   http://localhost/FutsalKu/
   ```
   *(Atau `http://localhost/futsalku/` tergantung nama folder tempat Anda menyimpannya).*

---

## 👨‍💻 Pengembang
- **MsRifai** - [GitHub Profile](https://github.com/MsRifai)
