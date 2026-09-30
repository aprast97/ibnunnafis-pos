# Project Specification — Sistem Informasi Penjualan dan Persediaan Kantin Ibnunnafis

## 1. Informasi Proyek

### Nama Proyek

**Sistem Informasi Penjualan dan Persediaan UMKM Kantin Ibnunnafis Depok**

### Konteks

Proyek ini merupakan pengembangan sistem informasi berbasis web untuk studi kasus UMKM Kantin Ibnunnafis Depok.

Sistem dikembangkan berdasarkan hasil observasi dan wawancara terhadap pengelola kantin.

Fokus utama sistem adalah digitalisasi:

- transaksi penjualan;
- pencatatan barang masuk;
- pengelolaan persediaan barang dagangan;
- stock opname;
- monitoring stok;
- pencatatan riwayat transaksi;
- pembuatan laporan penjualan.

Sistem tidak ditujukan sebagai sistem manufaktur atau sistem produksi makanan.

Kantin membeli barang dari supplier kemudian menjual kembali barang tersebut kepada konsumen.

---

# 2. Latar Belakang dan Masalah

Saat ini sebagian besar proses operasional Kantin Ibnunnafis masih dilakukan secara manual menggunakan buku.

Proses yang masih dilakukan secara manual antara lain:

1. Pencatatan transaksi penjualan.
2. Pencatatan barang masuk dari supplier.
3. Pemeriksaan kondisi stok.
4. Rekapitulasi penjualan.
5. Pembuatan laporan penjualan.
6. Pencatatan barang yang keluar akibat transaksi penjualan.

Barang masuk dicatat ketika supplier mengirimkan barang.

Namun, barang yang keluar karena transaksi penjualan belum tercatat secara terstruktur.

Pemeriksaan stok dilakukan secara manual pada malam hari setelah aktivitas penjualan selesai.

Laporan penjualan harian, mingguan, dan bulanan juga masih direkap secara manual.

Kondisi tersebut menyebabkan data transaksi dan persediaan belum terdokumentasi secara terstruktur dan terpusat.

Sistem dikembangkan untuk membantu digitalisasi proses tersebut.

---

# 3. Urgensi Digitalisasi

Digitalisasi diperlukan karena proses operasional kantin masih bergantung pada pencatatan manual.

Beberapa permasalahan utama:

- transaksi penjualan dicatat menggunakan buku;
- barang masuk dicatat menggunakan buku;
- barang keluar akibat penjualan belum tercatat secara terstruktur;
- pemeriksaan stok dilakukan secara manual;
- laporan penjualan dibuat secara manual;
- pencarian histori transaksi membutuhkan pemeriksaan catatan;
- rekapitulasi data membutuhkan pekerjaan manual;
- pergerakan stok belum dapat ditelusuri secara sistematis.

Kondisi tersebut menjadi semakin relevan karena barang yang dijual memiliki perputaran yang cukup cepat dan satu transaksi dapat terdiri dari banyak jenis barang.

Digitalisasi diharapkan dapat:

1. Mencatat transaksi penjualan secara terstruktur.
2. Mengurangi pekerjaan pencatatan manual.
3. Mengurangi ketergantungan terhadap buku catatan.
4. Mencatat perubahan stok secara otomatis.
5. Memudahkan monitoring stok.
6. Membantu mengetahui produk yang stoknya menipis.
7. Menyediakan histori transaksi.
8. Memudahkan pembuatan laporan penjualan.
9. Menyediakan data yang lebih mudah ditelusuri.
10. Menyediakan backup data digital.

---

# 4. Karakteristik Kantin

## 4.1 Jenis Barang

Barang yang dijual antara lain:

- makanan ringan;
- minuman;
- gorengan;
- mie instan;
- kebutuhan sehari-hari;
- alat tulis;
- kitab agama;
- buku;
- jajanan lainnya.

## 4.2 Jenis Persediaan

Sistem mengelola **stok barang dagangan**, bukan stok bahan baku.

Kantin tidak berfokus pada proses produksi makanan seperti manufaktur.

Barang diperoleh dari supplier dan kemudian dijual kembali kepada konsumen.

Contoh:

```text
Supplier
    ↓
Barang dagangan
    ↓
Kantin
    ↓
Penjualan
    ↓
Konsumen
````

Sistem tidak perlu memiliki modul warehouse/gudang khusus.

Tidak perlu membuat entitas `Warehouse`.

---

# 5. Karakteristik Operasional

Kantin memiliki beberapa periode operasional:

* saat jam istirahat sekolah;
* setelah Dzuhur;
* sekitar pukul 16:30;
* setelah kegiatan belajar malam, sekitar setelah pukul 21:00.

Aktivitas penjualan dilakukan secara langsung di kantin.

Sebagian besar barang dapat diambil sendiri oleh pembeli.

Untuk barang tertentu seperti kitab agama dan air panas, barang diberikan oleh petugas kantin.

Pembeli kemudian menuju kasir untuk melakukan pembayaran.

Pembayaran menggunakan uang tunai.

Saat ini kantin tidak menggunakan QRIS atau e-wallet.

---

# 6. Aktor dan Role Sistem

Sistem hanya memiliki dua role:

1. Kasir
2. Admin

Terdapat dua akun Kasir dan satu akun Admin.

Tidak perlu membuat fitur User Management sebagai fitur operasional karena jumlah akun bersifat terbatas dan tetap.

## 6.1 Kasir

### Karakteristik

Kasir merupakan pengguna yang terlibat langsung dalam kegiatan operasional kantin.

Kasir bertanggung jawab terhadap:

* transaksi penjualan;
* pencatatan barang masuk;
* pengelolaan produk;
* pengelolaan kategori;
* pengelolaan supplier;
* stock opname;
* monitoring stok;
* laporan operasional.

### Kewenangan Kasir

Kasir dapat:

* Login
* Melihat Dashboard
* Melihat stok
* Melihat notifikasi stok
* Melakukan transaksi penjualan
* Mencetak struk
* Membatalkan transaksi
* Melihat riwayat transaksi
* Mengelola produk
* Mengelola kategori
* Mengelola supplier
* Mencatat barang masuk
* Melakukan stock opname
* Melihat laporan
* Mencetak laporan

## 6.2 Admin

### Karakteristik

Admin merupakan pengguna yang bertugas melakukan monitoring terhadap aktivitas sistem dan kondisi operasional kantin.

Admin bersifat monitoring-only.

Admin tidak boleh melakukan perubahan data operasional.

### Kewenangan Admin

Admin dapat:

* Login
* Melihat Dashboard
* Melihat stok
* Melihat notifikasi stok
* Melihat riwayat transaksi
* Melihat laporan
* Mencetak laporan

### Admin tidak boleh:

* melakukan transaksi penjualan;
* membatalkan transaksi;
* mengubah produk;
* mengubah kategori;
* mengubah supplier;
* mencatat barang masuk;
* melakukan stock opname;
* mengubah data operasional.

---

# 7. Teknologi yang Digunakan

## 7.1 Frontend

Gunakan:

* Laravel Blade
* Bootstrap 5
* Vanilla JavaScript

Frontend tidak dibuat sebagai aplikasi SPA terpisah.

Blade digunakan sebagai server-side rendered template.

Bootstrap digunakan untuk komponen UI dan responsive layout.

Vanilla JavaScript digunakan untuk interaksi yang membutuhkan client-side behavior.

## 7.2 Backend

Gunakan:

* PHP
* Laravel

Laravel menangani:

* routing;
* controller;
* authentication;
* authorization;
* validation;
* business logic;
* database transaction;
* ORM;
* migration;
* model;
* session;
* reporting;
* server-side rendering melalui Blade.

## 7.3 Database

Gunakan:

**MySQL**

Gunakan database relational.

Database harus memiliki foreign key dan constraint yang sesuai.

## 7.4 Database Management

Gunakan:

**DBeaver**

DBeaver hanya digunakan sebagai database client/management tool.

DBeaver bukan pengganti Laravel, PHP runtime, atau MySQL server.

## 7.5 Arsitektur

Gunakan:

**Modular Monolith**

Jangan menggunakan microservices.

Frontend dan backend berada dalam satu aplikasi Laravel.

Konsep arsitektur:

```text
Browser
   ↓
Laravel Application
   ├── Routes
   ├── Controllers
   ├── Services / Business Logic
   ├── Models / Eloquent ORM
   ├── Blade Views
   └── Authentication / Authorization
   ↓
MySQL
```

Alasan menggunakan modular monolith:

* ukuran sistem relatif kecil;
* kebutuhan sistem tidak membutuhkan microservices;
* tim pengembang memiliki kemampuan yang berbeda;
* mengurangi kompleksitas deployment;
* lebih mudah dikembangkan dan dipelihara;
* sesuai dengan kebutuhan tugas akhir.

---

# 8. Batasan Sistem

Sistem memiliki batasan sebagai berikut:

1. Sistem digunakan khusus untuk Kantin Ibnunnafis Depok.
2. Sistem berbasis web.
3. Sistem berfokus pada penjualan dan persediaan barang dagangan.
4. Sistem tidak menangani proses produksi makanan.
5. Sistem tidak memiliki modul warehouse/gudang.
6. Sistem tidak menghitung laporan laba rugi.
7. Sistem hanya membutuhkan perhitungan omzet penjualan.
8. Pembayaran utama menggunakan uang tunai.
9. Payment gateway bukan bagian dari kebutuhan utama sistem.
10. QRIS/e-wallet tidak menjadi fokus sistem.
11. Sistem tidak memiliki fitur bundling recommendation.
12. Sistem tidak memiliki fitur rekomendasi produk berbasis AI.
13. Sistem tidak membutuhkan fitur user management untuk pengguna operasional.
14. Sistem tidak menggunakan microservices.
15. Sistem tidak perlu menggunakan Redis jika tidak ada kebutuhan teknis yang jelas.
16. Sistem tidak perlu menggunakan frontend framework seperti Vue, React, atau Svelte.
17. Sistem tidak perlu membuat API terpisah untuk frontend.

---

# 9. Modul Sistem

Modul utama:

## 9.1 Authentication

* Login
* Logout
* Role-based authorization

## 9.2 Dashboard

Menampilkan informasi ringkas:

* omzet hari ini;
* jumlah transaksi hari ini;
* jumlah produk;
* produk stok menipis;
* produk habis;
* ringkasan penjualan;
* informasi operasional lainnya.

Dashboard harus menyesuaikan role.

Kasir dan Admin dapat memiliki tampilan dashboard yang sama secara umum, tetapi hak akses tetap berbeda.

## 9.3 Product Management

Kasir dapat:

* melihat produk;
* menambah produk;
* mengubah produk;
* menonaktifkan produk;
* mengatur kategori;
* mengatur harga beli;
* mengatur harga jual;
* mengatur stok minimum;
* mengatur satuan.

Produk yang sudah memiliki histori transaksi sebaiknya tidak dihapus secara hard delete.

Gunakan status aktif/nonaktif.

## 9.4 Category Management

Kasir dapat:

* melihat kategori;
* menambah kategori;
* mengubah kategori;
* menonaktifkan kategori.

## 9.5 Supplier Management

Kasir dapat:

* melihat supplier;
* menambah supplier;
* mengubah supplier;
* menonaktifkan supplier.

Data supplier minimal:

* nama;
* nomor telepon;
* alamat.

Contoh supplier:

* Joyday;
* toko pasar;
* supplier lainnya.

---

# 10. Satuan Produk dan Konversi

Produk dapat dibeli menggunakan satuan yang berbeda dengan satuan penjualan.

Contoh:

```text
1 Dus = 24 Pcs
```

Kantin dapat:

```text
Membeli:
1 Dus

Menjual:
1 Pcs
```

Sistem harus memiliki konsep:

* base unit;
* purchase unit;
* sales unit;
* conversion factor.

Contoh:

```text
Produk: Minuman A

Base Unit:
Pcs

Purchase Unit:
Dus

Conversion:
1 Dus = 24 Pcs
```

Jika kantin menerima:

```text
2 Dus
```

Maka stok bertambah:

```text
2 × 24 = 48 Pcs
```

Jika kemudian terjual:

```text
3 Pcs
```

Maka stok menjadi:

```text
48 - 3 = 45 Pcs
```

Sistem harus menyimpan conversion factor pada detail transaksi agar histori tidak berubah apabila konfigurasi satuan diubah di kemudian hari.

---

# 11. Barang Masuk

Barang masuk terjadi ketika supplier mengirimkan barang ke kantin.

## Alur bisnis

```text
Supplier mengirim barang
        ↓
Kasir menerima barang
        ↓
Kasir memeriksa barang
        ↓
Kasir memeriksa nota
        ↓
Barang sesuai?
   ┌────┴────┐
  Ya        Tidak
   ↓          ↓
Konfirmasi   Catat
penerimaan   ketidaksesuaian
   ↓
Simpan transaksi pembelian
   ↓
Tambah stok
   ↓
Catat stock movement
```

Jika barang sesuai:

1. Kasir memilih supplier.
2. Kasir memasukkan nomor nota.
3. Kasir memasukkan barang.
4. Kasir memasukkan jumlah.
5. Kasir memilih satuan pembelian.
6. Sistem menghitung jumlah base unit.
7. Kasir memasukkan harga beli.
8. Sistem menghitung subtotal.
9. Sistem menghitung total pembelian.
10. Data disimpan.
11. Stok bertambah.
12. Stock movement dicatat.

Jika barang tidak sesuai:

* barang dapat dikembalikan kepada supplier;
* atau disimpan sementara;
* dan ketidaksesuaian dilaporkan kepada supervisor.

Barang yang belum dikonfirmasi tidak boleh langsung menambah stok.

---

# 12. Penjualan

Penjualan merupakan proses utama sistem.

## Alur bisnis

```text
Pembeli memilih barang
        ↓
Pembeli menuju kasir
        ↓
Kasir memilih produk
        ↓
Masukkan jumlah
        ↓
Sistem validasi stok
        ↓
Tambah item lain?
   ┌────┴────┐
  Ya        Tidak
   ↓          ↓
Tambah item   Hitung total
                ↓
          Input uang bayar
                ↓
       Pembayaran cukup?
          ┌────┴────┐
         Tidak      Ya
           ↓         ↓
       Tampilkan   Hitung
        error      kembalian
                     ↓
              Simpan transaksi
                     ↓
               Kurangi stok
                     ↓
             Catat stock movement
                     ↓
                Cetak struk
```

## Ketentuan penjualan

1. Satu transaksi dapat memiliki banyak produk.
2. Produk dapat menggunakan satuan penjualan tertentu.
3. Sistem harus memvalidasi ketersediaan stok.
4. Stok tidak boleh menjadi negatif.
5. Sistem menghitung subtotal setiap item.
6. Sistem menghitung total transaksi.
7. Kasir memasukkan jumlah uang yang diterima.
8. Sistem menghitung kembalian.
9. Pembayaran kurang harus ditolak.
10. Setelah transaksi berhasil, stok dikurangi.
11. Stock movement harus dibuat.
12. Harga pada transaksi harus disimpan sebagai snapshot.
13. Perubahan harga produk di kemudian hari tidak boleh mengubah histori transaksi.

---

# 13. Pembatalan Transaksi

Transaksi dapat dibatalkan oleh Kasir.

Transaksi tidak boleh dihapus secara langsung.

Gunakan status transaksi.

Contoh:

```text
COMPLETED
CANCELLED
```

## Alur pembatalan

```text
Transaksi COMPLETED
        ↓
Kasir memilih Batalkan
        ↓
Kasir memasukkan alasan
        ↓
Sistem mengubah status menjadi CANCELLED
        ↓
Stok dikembalikan
        ↓
Stock movement dicatat
        ↓
Histori transaksi tetap tersedia
```

Data transaksi tetap disimpan untuk kebutuhan audit dan histori.

---

# 14. Stok

Stok yang dikelola adalah stok barang dagangan.

Sumber perubahan stok:

```text
Barang Masuk
     ↓
STOK BERTAMBAH

Penjualan
     ↓
STOK BERKURANG

Pembatalan Penjualan
     ↓
STOK BERTAMBAH KEMBALI

Stock Opname
     ↓
Penyesuaian jika terdapat selisih
```

Setiap perubahan stok harus dapat ditelusuri melalui `stock_movements`.

Minimal informasi stock movement:

* produk;
* user;
* jenis pergerakan;
* referensi transaksi;
* jumlah;
* stok sebelum;
* stok sesudah;
* catatan;
* waktu.

---

# 15. Low Stock Notification

Setiap produk memiliki:

```text
current_stock
minimum_stock
```

Aturan:

```text
current_stock <= minimum_stock
→ Stok Menipis
```

```text
current_stock = 0
→ Stok Habis
```

Notifikasi stok tidak harus disimpan sebagai tabel notification.

Status dapat dihitung berdasarkan kondisi stok saat ini.

Contoh:

```text
Indomie
Stok: 5
Minimum: 10

Status:
STOK MENIPIS
```

Admin dan Kasir dapat melihat informasi tersebut.

---

# 16. Stock Opname

Stock opname dilakukan untuk membandingkan:

```text
Stok menurut sistem
vs
Stok fisik
```

## Alur

```text
Kasir memulai Stock Opname
        ↓
Sistem menampilkan produk
        ↓
Sistem menampilkan stok sistem
        ↓
Kasir menghitung stok fisik
        ↓
Kasir memasukkan stok fisik
        ↓
Sistem menghitung selisih
        ↓
Ada selisih?
   ┌────┴────┐
  Ya        Tidak
   ↓          ↓
Penyesuaian   Konfirmasi
stok          stok
   ↓          ↓
Catat stock movement
        ↓
Simpan hasil opname
```

Rumus:

```text
difference = physical_stock - system_stock
```

Contoh:

```text
System Stock  = 20
Physical Stock = 18

Difference = -2
```

Sistem kemudian dapat menyesuaikan stok menjadi 18 setelah Kasir melakukan konfirmasi.

---

# 17. Laporan

Sistem menyediakan laporan penjualan:

* harian;
* mingguan;
* bulanan.

Laporan berfokus pada omzet, bukan laba rugi.

Informasi minimal:

* periode;
* jumlah transaksi;
* total omzet;
* daftar transaksi;
* produk terjual;
* jumlah produk terjual.

## Laporan harian

Contoh:

```text
Tanggal: 27 September 2026

Jumlah transaksi : 35
Total omzet      : Rp450.000
```

## Laporan mingguan

Menampilkan agregasi penjualan selama satu minggu.

## Laporan bulanan

Menampilkan agregasi penjualan selama satu bulan.

Admin dan Kasir dapat melihat laporan.

Admin dan Kasir dapat mencetak laporan.

---

# 18. Struk Penjualan

Setelah transaksi berhasil, Kasir dapat mencetak struk.

Struk minimal berisi:

* nama kantin;
* nomor transaksi;
* tanggal dan waktu;
* daftar barang;
* jumlah;
* harga satuan;
* subtotal;
* total;
* uang diterima;
* kembalian.

Pembayaran menggunakan uang tunai.

---

# 19. Riwayat Transaksi

Sistem menyimpan histori transaksi.

Informasi:

* nomor transaksi;
* tanggal;
* kasir;
* total;
* status;
* waktu transaksi;
* alasan pembatalan jika dibatalkan.

Transaksi yang dibatalkan tetap ditampilkan dalam histori.

Tujuannya agar transaksi tidak hilang dan tetap dapat ditelusuri.

---

# 20. Harga Produk

Harga produk dapat berubah mengikuti kondisi pasar.

Produk memiliki:

* harga beli;
* harga jual.

Namun harga pada transaksi harus disimpan sebagai snapshot.

Contoh:

```text
Harga produk saat ini:
Rp3.000

Transaksi tanggal 1:
Rp3.000
```

Kemudian harga berubah:

```text
Harga produk:
Rp3.500
```

Histori transaksi tanggal 1 tetap:

```text
Rp3.000
```

Jangan menghitung ulang histori transaksi menggunakan harga produk saat ini.

---

# 21. Model Data Utama

Entitas utama:

```text
Role
User
Category
Unit
Product
ProductUnit
Supplier
Purchase
PurchaseItem
Sale
SaleItem
StockMovement
StockOpname
StockOpnameItem
```

## Role

Menyimpan:

* id;
* name;
* description.

Role:

```text
Kasir
Admin
```

## User

Menyimpan:

* id;
* role_id;
* name;
* email;
* password;
* created_at;
* updated_at.

## Category

Menyimpan kategori produk.

## Unit

Menyimpan satuan:

Contoh:

* pcs;
* botol;
* dus;
* bungkus;
* pak.

## Product

Menyimpan:

* category_id;
* name;
* sku;
* purchase_price;
* selling_price;
* current_stock;
* minimum_stock;
* base_unit_id;
* is_active.

## ProductUnit

Digunakan untuk konversi satuan.

Menyimpan:

* product_id;
* unit_id;
* conversion_factor;
* selling_price.

## Supplier

Menyimpan:

* name;
* phone;
* address.

## Purchase

Menyimpan header barang masuk.

## PurchaseItem

Menyimpan detail barang masuk.

## Sale

Menyimpan header transaksi penjualan.

## SaleItem

Menyimpan detail produk yang dijual.

## StockMovement

Mencatat setiap perubahan stok.

## StockOpname

Menyimpan header stock opname.

## StockOpnameItem

Menyimpan detail hasil pemeriksaan stok.

---

# 22. Aturan Database Penting

Gunakan foreign key.

Gunakan database transaction untuk proses yang memengaruhi beberapa tabel sekaligus.

Contoh transaksi penjualan:

```text
BEGIN TRANSACTION

1. Validasi stok
2. Simpan Sale
3. Simpan SaleItem
4. Kurangi Product.current_stock
5. Simpan StockMovement

COMMIT
```

Jika salah satu proses gagal:

```text
ROLLBACK
```

Tidak boleh terjadi kondisi:

```text
Sale berhasil
tetapi stok gagal dikurangi
```

atau:

```text
Stok berkurang
tetapi Sale gagal disimpan
```

Keduanya harus bersifat atomic.

---

# 23. Struktur Modul Laravel

Gunakan struktur Laravel yang sederhana dan mudah dipahami.

Contoh:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── SupplierController.php
│   │   ├── PurchaseController.php
│   │   ├── SaleController.php
│   │   ├── StockController.php
│   │   ├── StockOpnameController.php
│   │   └── ReportController.php
│   │
│   └── Requests/
│
├── Models/
│   ├── Role.php
│   ├── User.php
│   ├── Category.php
│   ├── Unit.php
│   ├── Product.php
│   ├── ProductUnit.php
│   ├── Supplier.php
│   ├── Purchase.php
│   ├── PurchaseItem.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── StockMovement.php
│   ├── StockOpname.php
│   └── StockOpnameItem.php
│
└── Services/
    ├── SaleService.php
    ├── StockService.php
    ├── PurchaseService.php
    └── ReportService.php
```

Services digunakan apabila business logic sudah cukup kompleks.

Jangan membuat abstraction yang tidak diperlukan.

---

# 24. Authorization

Gunakan role-based authorization.

Contoh:

```text
Kasir:
- CRUD operasional
- transaksi
- stock opname

Admin:
- read-only monitoring
```

Authorization harus dilakukan pada backend.

Jangan hanya menyembunyikan tombol berdasarkan role di frontend.

Contoh:

```text
Admin tidak boleh melakukan POST/PUT/DELETE
untuk modul operasional walaupun URL diketahui.
```

---

# 25. UI/UX

Gunakan desain yang sederhana dan cocok untuk aplikasi internal kantin.

Prioritas:

1. Mudah digunakan.
2. Cepat melakukan transaksi.
3. Informasi stok mudah terlihat.
4. Form tidak terlalu kompleks.
5. Responsive.
6. Konsisten.
7. Memiliki feedback setelah operasi.

Gunakan:

* Bootstrap 5;
* modal;
* alert;
* badge status;
* table;
* form validation;
* pagination jika diperlukan;
* search/filter.

POS/sales screen harus menjadi salah satu halaman yang paling cepat digunakan.

---

# 26. Dashboard

Dashboard dapat menampilkan:

```text
+--------------------+
| Omzet Hari Ini     |
+--------------------+

+--------------------+
| Transaksi Hari Ini |
+--------------------+

+--------------------+
| Produk Stok Menipis|
+--------------------+

+--------------------+
| Produk Habis       |
+--------------------+
```

Tambahkan tabel:

* produk stok menipis;
* transaksi terbaru;
* ringkasan penjualan.

Jangan membuat dashboard terlalu kompleks.

---

# 27. Keamanan

Minimal implementasikan:

* password hashing;
* authentication;
* authorization;
* CSRF protection;
* validation;
* server-side authorization;
* database constraints;
* session security;
* input sanitization/escaping melalui mekanisme Laravel.

Password tidak boleh disimpan dalam bentuk plaintext.

---

# 28. Non-Functional Requirements

## Performance

Sistem harus dapat digunakan untuk transaksi kantin sehari-hari tanpa proses yang terasa berat.

## Usability

Kasir harus dapat melakukan transaksi dengan langkah seminimal mungkin.

## Reliability

Perubahan stok dan transaksi harus konsisten.

## Maintainability

Kode harus menggunakan struktur Laravel yang standar dan mudah dipahami.

## Security

User hanya dapat melakukan operasi sesuai role.

## Data Integrity

Data transaksi dan stok harus konsisten.

---

# 29. Alur Bisnis Utama

## 29.1 Penjualan

```text
Buyer
  ↓
Pilih barang
  ↓
Kasir
  ↓
Input produk
  ↓
Validasi stok
  ↓
Hitung total
  ↓
Input uang
  ↓
Hitung kembalian
  ↓
Simpan transaksi
  ↓
Kurangi stok
  ↓
Stock movement
  ↓
Cetak struk
```

## 29.2 Barang Masuk

```text
Supplier
  ↓
Mengirim barang
  ↓
Kasir memeriksa barang
  ↓
Cocok dengan nota?
  ↓
Konfirmasi
  ↓
Simpan pembelian
  ↓
Tambah stok
  ↓
Stock movement
```

## 29.3 Stock Opname

```text
Kasir
  ↓
Mulai stock opname
  ↓
Lihat stok sistem
  ↓
Hitung stok fisik
  ↓
Input stok fisik
  ↓
Sistem menghitung selisih
  ↓
Konfirmasi
  ↓
Penyesuaian stok
  ↓
Stock movement
```

## 29.4 Pembatalan

```text
Kasir
  ↓
Pilih transaksi
  ↓
Batalkan
  ↓
Input alasan
  ↓
Status CANCELLED
  ↓
Stok dikembalikan
  ↓
Stock movement
```

## 29.5 Laporan

```text
Kasir/Admin
     ↓
Pilih laporan
     ↓
Pilih periode
     ↓
Sistem mengambil transaksi
     ↓
Filter berdasarkan periode
     ↓
Hitung omzet
     ↓
Tampilkan laporan
     ↓
Opsional: Cetak
```

---

# 30. Fitur yang TIDAK BOLEH Ditambahkan Tanpa Persetujuan

Jangan menambahkan fitur berikut hanya untuk membuat aplikasi terlihat lebih kompleks:

* microservices;
* warehouse management;
* accounting;
* laporan laba rugi;
* payment gateway;
* QRIS;
* e-wallet;
* AI recommendation;
* bundling recommendation;
* machine learning;
* forecasting;
* loyalty program;
* marketplace;
* multi-cabang;
* multi-gudang;
* fitur produksi makanan;
* resep/BOM bahan baku;
* fitur delivery;
* fitur customer account.

Jika ada kebutuhan baru, diskusikan terlebih dahulu sebelum mengubah scope.

---

# 31. Prinsip Pengembangan

AI agent harus mengikuti prinsip:

### 1. Jangan overengineering

Sistem harus sederhana tetapi memenuhi kebutuhan bisnis.

### 2. Prioritaskan kebutuhan nyata

Setiap fitur harus memiliki alasan berdasarkan proses bisnis Kantin Ibnunnafis.

### 3. Jangan membuat fitur hanya karena teknologi memungkinkan

Teknologi digunakan untuk menyelesaikan masalah, bukan sebaliknya.

### 4. Gunakan Laravel secara native

Utamakan:

* Eloquent;
* Form Request;
* Middleware;
* Policy/Gate;
* Migration;
* Blade;
* Validation;
* Database Transaction.

### 5. Hindari dependensi yang tidak diperlukan

Jangan memasang package hanya untuk kebutuhan sederhana yang dapat diselesaikan dengan Laravel.

### 6. Jaga integritas stok

Stok adalah salah satu bagian terpenting sistem.

Semua perubahan stok harus dapat ditelusuri.

### 7. Jangan menghapus histori transaksi

Transaksi yang dibatalkan tetap harus tersimpan.

### 8. Pisahkan role dan actor

Kasir dan Admin bukan class database yang berbeda.

Gunakan:

```text
Role
  ↓
User
```

Dengan nilai:

```text
Kasir
Admin
```

---

# 32. Prioritas Implementasi

Implementasi dilakukan bertahap.

## Phase 1 — Foundation

* Laravel setup
* MySQL setup
* authentication
* role authorization
* database migration
* models
* seeders

## Phase 2 — Master Data

* category
* unit
* product
* product unit/conversion
* supplier

## Phase 3 — Inventory

* barang masuk
* purchase
* purchase items
* stock movement
* stock monitoring
* low stock

## Phase 4 — Sales

* POS
* multi-item transaction
* stock validation
* cash payment
* change calculation
* transaction storage
* stock reduction
* receipt

## Phase 5 — Cancellation

* transaction history
* cancellation
* stock restoration
* audit trail

## Phase 6 — Stock Opname

* stock opname
* physical stock
* difference
* stock adjustment
* stock movement

## Phase 7 — Reports

* daily report
* weekly report
* monthly report
* print report

## Phase 8 — Dashboard

* omzet
* transaction count
* stock status
* low stock
* recent transactions

## Phase 9 — Testing

Test:

* authentication;
* authorization;
* sales;
* insufficient stock;
* payment insufficient;
* payment change;
* multi-item sales;
* stock reduction;
* purchase;
* stock addition;
* cancellation;
* stock restoration;
* stock opname;
* report calculation.

---

# 33. Acceptance Criteria

Sistem dianggap memenuhi kebutuhan utama apabila:

### Authentication

* Kasir dapat login.
* Admin dapat login.
* User tidak dapat mengakses halaman tanpa autentikasi.

### Authorization

* Kasir dapat melakukan operasi operasional.
* Admin hanya dapat monitoring.
* Admin tidak dapat melakukan perubahan data operasional.

### Sales

* Kasir dapat membuat transaksi multi-item.
* Sistem memvalidasi stok.
* Sistem menghitung total.
* Sistem menghitung kembalian.
* Pembayaran kurang ditolak.
* Transaksi tersimpan.
* Stok berkurang.
* Stock movement tercatat.

### Purchase

* Kasir dapat mencatat barang masuk.
* Sistem dapat menggunakan satuan pembelian.
* Sistem menghitung conversion factor.
* Stok bertambah.
* Stock movement tercatat.

### Cancellation

* Kasir dapat membatalkan transaksi.
* Transaksi tidak dihapus.
* Status berubah menjadi CANCELLED.
* Alasan pembatalan disimpan.
* Stok dikembalikan.
* Stock movement tercatat.

### Stock Opname

* Kasir dapat membuat stock opname.
* Sistem menampilkan stok sistem.
* Kasir dapat memasukkan stok fisik.
* Sistem menghitung selisih.
* Stok dapat disesuaikan setelah konfirmasi.

### Reports

* Laporan harian tersedia.
* Laporan mingguan tersedia.
* Laporan bulanan tersedia.
* Total omzet dihitung dengan benar.
* Laporan dapat dicetak.

---

# 34. Prinsip Utama Sistem

Sistem ini bukan sekadar aplikasi kasir.

Sistem merupakan aplikasi untuk **digitalisasi proses penjualan dan pengelolaan persediaan barang dagangan Kantin Ibnunnafis**.

Masalah utama yang harus diselesaikan adalah:

```text
Pencatatan manual
       ↓
Data tidak terstruktur
       ↓
Sulit melakukan rekap
       ↓
Sulit memantau pergerakan stok
       ↓
Sulit menelusuri histori
       ↓
Digitalisasi
       ↓
Data transaksi terstruktur
       ↓
Stok diperbarui otomatis
       ↓
Stock opname terdokumentasi
       ↓
Laporan dapat dibuat otomatis
```

Semua keputusan teknis harus kembali kepada tujuan tersebut.

````
