# 🛒 Kantin Ibnunnafis - Point of Sale (POS) & Inventory System

Sistem Informasi Kasir dan Manajemen Inventaris Berbasis Web untuk **Kantin Ibnunnafis Pondok Pesantren**. Aplikasi ini dirancang untuk mempermudah transaksi penjualan harian, pengelolaan stok barang multi-satuan, pencatatan pembelian (kulakan), stock opname, laporan laba rugi, serta pencetakan struk termal.

🌐 **Live URL Production**: [https://kantin-ibnunnafis.vercel.app](https://kantin-ibnunnafis.vercel.app)

---

## 📑 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Arsitektur & Tech Stack](#-arsitektur--tech-stack)
3. [Akun Pengguna Bawaan (Default Credentials)](#-akun-pengguna-bawaan)
4. [Instalasi & Menjalankan di Lokal](#-instalasi--menjalankan-di-lokal)
5. [Struktur Database & Relasi](#-struktur-database--relasi)
6. [Fitur Cetak Struk Kasir](#-fitur-cetak-struk-kasir)
7. [Panduan Deployment Cloud (Vercel + Aiven MySQL)](#-panduan-deployment-cloud)
8. [Testing & Pengujian Otomatis](#-testing--pengujian-otomatis)

---

## ✨ Fitur Utama

### 1. Transaksi Kasir (POS) yang Cepat & Intuitif
- **Pencarian Cepat & Barcode**: Input produk via scanner barcode atau pencarian nama produk secara instan.
- **Dukungan Multi-Satuan**: Penjualan fleksibel berdasarkan unit (contoh: Pcs, Renceng, Dus, Porsi).
- **Perhitungan Kembalian Otomatis**: Menghitung total belanja, diskon, dan uang kembalian secara real-time.
- **Pencetakan Struk Thermal**: Format struk 58mm & 80mm yang kompatibel dengan browser print dialog dan thermal printer Bluetooth / USB.

### 2. Manajemen Inventaris & Stok Barang
- **Kategori & Satuan Produk**: Pengelompokan barang berdasarkan jenis (Makanan, Minuman, Snack, ATK, Kitab).
- **Konversi Multi-Satuan Produk**: Satu produk dapat memiliki beberapa satuan jual dengan pengali isi (konversi rasio) dan harga berbeda.
- **Pencatatan Pembelian (Kulakan)**: Input barang masuk dari supplier beserta pembaruan harga modal secara otomatis.
- **Stock Opname**: Penyesuaian fisik stok harian/mingguan dengan catatan selisih stok.
- **Kartu Stok (Stock Movements)**: Riwayat pergerakan stok (masuk, keluar, rusak, penyesuaian).

### 3. Laporan & Rekapitulasi (Monitoring Admin)
- **Dashboard Ringkasan**: Statistik omset harian/bulanan, laba kotor, dan total transaksi.
- **Laporan Penjualan**: Filter berdasarkan rentang tanggal, kasir yang bertugas, dan status transaksi.
- **Laporan Laba Rugi**: Analisis margin per barang dan laba operasional kantin.
- **Export Data**: Ekspor laporan ke format Excel / PDF / Cetak langsung.

### 4. Hak Akses (Multi-Role Authentication)
- **Role Kasir**: Dikhususkan untuk operasional kasir, transaksi penjualan, dan cek stok cepat.
- **Role Admin**: Memiliki hak akses penuh mencakup manajemen master data barang, supplier, pembelian, opname, user, dan laporan keuangan.

---

## 🛠 Arsitektur & Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 11.x | PHP 8.4+ |
| **Frontend UI** | Blade + Tailwind CSS / Alpine.js | Desain responsif, clean & modern |
| **Web Server / Runtime** | FrankenPHP (Caddy Server) | High-performance Caddy server dengan worker mode |
| **Containerization** | Docker | Multi-stage build via `Dockerfile.vercel` |
| **Database** | MySQL 8.0+ | Managed cloud via Aiven Cloud / Local MySQL |
| **Hosting Platform** | Vercel Serverless Containers | CI/CD otomatis terhubung dengan repositori GitHub |

---

## 🔑 Akun Pengguna Bawaan

Aplikasi menggunakan autentikasi **Username** (ID unik numerik) dan **Password**:

| Role | Nama Lengkap | Username | Password | Hak Akses |
| :--- | :--- | :---: | :---: | :--- |
| **Admin** | Ummi Hj. Romlah Dana Sidiq Lc | `84921` | `84921` | Akses Penuh (Master Data, Laporan, Admin) |
| **Kasir 1** | Raudhatul Abila Sunarya | `39147` | `39147` | Kasir & Operasional Transaksi Harian |
| **Kasir 2** | Alya Nur Fadhilah | `72583` | `72583` | Kasir & Operasional Transaksi Harian |

---

## 💻 Instalasi & Menjalankan di Lokal

### Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `bcmath`, `intl`, `mbstring`, `fileinfo`)
- Composer
- Node.js & NPM (opsional jika compile frontend)
- MySQL Server (XAMPP / Laragon / Docker)

### Langkah Setup
1. **Clone Repositori**:
   ```bash
   git clone https://github.com/aprast97/ibnunnafis-pos.git
   cd ibnunnafis-pos
   ```

2. **Install Dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin berkas `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`**:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kantin_ibnunnafis
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi & Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Buat Symlink Storage**:
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka di browser: `http://localhost:8000`

---

## 🗄 Struktur Database & Relasi

Aplikasi ini memiliki 17 tabel migrasi utama:
1. `users` & `roles`: Autentikasi dan hak akses role (`Admin` vs `Kasir`).
2. `categories` & `units`: Master klasifikasi barang dan satuan acuan.
3. `suppliers`: Data pemasok / tempat kulakan barang.
4. `products`: Master produk kantin (kode barcode, nama, harga dasar, stok minimun, foto).
5. `product_units`: Multi-satuan tiap produk (cth: Satuan Dus isi 24 Pcs dengan harga jual tersendiri).
6. `purchases` & `purchase_items`: Pencatatan nota belanja / kulakan dari supplier.
7. `sales` & `sale_items`: Riwayat transaksi penjualan, nominal bayar, kembalian, dan kasir penanggung jawab.
8. `stock_movements`: Log mutasi keluar-masuk stok barang secara auditabel.
9. `stock_opnames` & `stock_opname_items`: Berita acara penyesuaian stok fisik periodik.
10. `sessions`, `cache`, `jobs`: Tabel penunjang performa dan sesi Laravel.

---

## 🖨 Fitur Cetak Struk Kasir

Karena aplikasi di-deploy di cloud publik (HTTPS) pada platform Vercel, pencetakan struk dirancang ramah cloud dan perangkat kasir:
- Menggunakan **Standard Browser Print Dialog** (`window.print()`) yang dikombinasikan dengan stylesheet khusus `@media print`.
- Format kertas teroptimasi untuk **Thermal Paper 58mm** dan **80mm**.
- Dapat dihubungkan ke:
  - Thermal Printer USB (di-set sebagai printer default di PC kasir).
  - Thermal Printer Bluetooth / WiFi dari Smartphone atau Tablet Kasir.
- Tidak memerlukan driver server-side atau koneksi jaringan lokal langsung dari cloud Vercel ke printer.

---

## ☁ Panduan Deployment Cloud

Aplikasi ini menggunakan perpaduan **Vercel** (sebagai serverless web application container) dan **Aiven.io** (sebagai managed cloud MySQL).

### Komponen Deployment:
1. `Dockerfile.vercel`: Berbasis image `dunglas/frankenphp:latest-php8.4-alpine`, mengompilasi dependensi Laravel dan mengoptimalkan cache konfigurasi.
2. `Caddyfile`: Konfigurasi server web Caddy yang mem-forward request ke FrankenPHP pada port 80.
3. `vercel.json`: Mendefinisikan runtime `docker` pada spesifikasi Vercel.

### Environment Variables yang Diperlukan di Vercel:
```ini
APP_NAME="Kantin Ibnunnafis"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kantin-ibnunnafis.vercel.app
APP_KEY=base64:... (Sesuai app key proyek)

DB_CONNECTION=mysql
DB_HOST=mysql-14cecd3c-kantin-ibnunnafis.f.aivencloud.com
DB_PORT=13716
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=<password_aiven_anda>

SESSION_DRIVER=database
CACHE_STORE=database
```

---

## 🧪 Testing & Pengujian Otomatis

Proyek ini dilengkapi dengan pengujian otomatis komprehensif menggunakan **Pest PHP / PHPUnit**:
```bash
php artisan test
```
- **Total Pengujian**: 56 Tests lolos.
- **Total Asersi**: 198 Assertions tanpa error.
- Pengujian mencakup alur autentikasi, transaksi kasir, perhitungan stok bertingkat, validasi input, serta otorisasi admin.

---

## 👨‍💻 Tim Pengembang & Kontak
- **Proyek**: Sistem Informasi Kasir Kantin Ibnunnafis
- **Tujuan**: Penugasan & Digitalisasi Operasional Kantin Pondok Pesantren
- **Lisensi**: Open-source untuk kebutuhan edukasi dan operasional pesantren.
