# Apotek App

Sistem Informasi Manajemen Apotek Modern yang dibangun menggunakan **Laravel 12** dan **FilamentPHP v3**. Aplikasi ini dirancang untuk memudahkan pengelolaan stok obat, distributor, dan transaksi penjualan dengan antarmuka yang bersih dan responsif.

## 🚀 Fitur Utama

### 1. Manajemen Inventaris
- **Data Obat**: Pengelolaan lengkap data obat termasuk kode, nama, harga, stok, dan satuan.
- **Distributor**: Manajemen data supplier/distributor obat.
- **Monitoring Stok**: Widget dashboard untuk memantau stok yang menipis secara real-time.

### 2. Transaksi (Point of Sales)
- **Input Penjualan**: Form transaksi yang intuitif dengan fitur *auto-calculate*.
- **Live Update**: Total harga terhitung otomatis saat jumlah barang diubah.
- **Validasi Stok**: Sistem mencegah penjualan melebihi stok yang tersedia.
- **Struk Digital**: Kemampuan untuk mencetak struk transaksi (Print View).

### 3. Katalog Publik (Frontend)
- **Katalog Online**: Halaman depan yang menampilkan daftar obat tersedia untuk pelanggan.
- **Pemesanan via API**: Integrasi frontend menggunakan Alpine.js yang berkomunikasi langsung dengan API backend.
- **Keranjang Belanja**: Fitur "Add to Cart" sederhana tanpa perlu login.

### 4. Laporan & Statistik
- **Dashboard Admin**: Ringkasan statistik penjualan, total transaksi, dan jumlah distributor.
- **Riwayat Transaksi**: Log lengkap semua transaksi yang terjadi.

## 🛠️ Teknologi yang Digunakan

- **Backend Framework**: Laravel 12
- **Admin Panel**: FilamentPHP v3
- **Frontend**: Blade Templates + Alpine.js
- **Database**: MySQL / SQLite
- **Styling**: Tailwind CSS

## 📦 Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lokal komputer Anda:

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/apotek-app.git
   cd apotek-app
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database**
   Jalankan migrasi untuk membuat tabel-tabel yang diperlukan.
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di: `http://localhost:8000`
   Akses admin panel di: `http://localhost:8000/admin`

## 👤 Akun Demo

Jika menggunakan seeder bawaan (`DatabaseSeeder`), Anda dapat login menggunakan:

- **Email**: `admin@apotek.com`
- **Password**: `password` (default password factory Laravel)

## 📝 Lisensi

Project ini bersifat open-source di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
