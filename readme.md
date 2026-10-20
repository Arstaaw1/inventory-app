# Documentation: Inventory Management System (Mini Project 3)

Aplikasi Web Manajemen Inventaris dibangun menggunakan bahasa pemrogramam PHP dan basis data MySQL. Aplikasi ini dikembangkan untuk mengelola persediaan barang, transaksi pergerakan stok, pelaporan ringkasan inventaris, serta pembatasan hak akses pengguna berdasarkan peran (Role-Based Access Control).

## 1. Modul dan Fitur Utama

- Autentikasi dan Otorisasi: Fitur autentikasi pengguna menggunakan enkripsi kata sandi (Password Hashing) serta manajemen sesi aman. Pengujian hak akses membedakan peran antara Admin dan Staff.
- Pengelolaan Data Produk dan Kategori: Fitur manipulasi data produk (CRUD) yang mencakup penentuan batas stok minimum untuk notifikasi restock.
- Pencarian, Filter, dan Pagination: Fitur pencarian barang berdasarkan Nama atau SKU, penyaringan data berdasarkan kategori, serta pembagian data tabel menggunakan teknik pagination.
- Transaksi Pergerakan Stok: Transaksi Barang Masuk dan Barang Keluar yang dijamin oleh database transaction (beginTransaction, commit, dan rollback) guna menjaga konsistensi antara data stok produk dan catatan riwayat pergerakan.
- Ringkasan Dashboard: Menampilkan laporan agregat secara real-time seperti total produk, estimasi nilai total inventaris, status peringatan stok rendah, dan total pergerakan barang.
- Keamanan Aplikasi: Penggunaan PDO Prepared Statement untuk mencegah ancaman SQL Injection serta pemformatan output (htmlspecialchars) untuk mencegah Cross-Site Scripting (XSS).

## 2. Entity Relationship Diagram (ERD)

![Entity Relationship Diagram](public/assets/ERD_Inventory%20system.jpg)

### Deskripsi Relasi Antar Tabel:
1. Tabel users ke stock_movements (One-to-Many): Satu pengguna/petugas dapat mencatat banyak transaksi pergerakan stok barang.
2. Tabel categories ke products (One-to-Many): Satu kategori dapat menampung banyak data produk.
3. Tabel products ke stock_movements (One-to-Many): Satu produk memiliki banyak riwayat catatan barang masuk dan barang keluar.

## 3. Aturan Bisnis (Business Rules)

1. Keunikan Entitas: Kode SKU produk dan nama pengguna (username) bersifat unik dan tidak boleh sama di dalam sistem.
2. Batas Minimum Stok: Jumlah stok produk tidak boleh bernilai negatif. Jika jumlah stok berada di bawah atau sama dengan nilai minimum_stock, sistem otomatis menetapkan status "Stok Rendah".
3. Validasi Barang Keluar: Jumlah pengeluaran barang pada transaksi tidak boleh melebihi jumlah stok yang tersedia saat ini di database.
4. Hak Akses Peran:
   - Admin: Memiliki hak akses penuh untuk menambah, mengedit, menghapus produk, serta melakukan transaksi.
   - Staff: Memiliki hak akses terbatas hanya untuk melihat daftar produk dan mencatat transaksi barang masuk atau keluar.

## 4. Skenario Pengujian Sistem

1. Pengujian Validation Error:
   - Skenario: Menginputkan jumlah barang keluar melebihi stok produk yang tersedia.
   - Hasil: Sistem menolak transaksi, membatalkan perintah database (rollback), dan menampilkan pesan kesalahan "Stok tidak mencukupi".
2. Pengujian Akses Terlarang (Forbidden Access):
   - Skenario: Pengguna dengan peran Staff mencoba mengakses URL administrasi khusus Admin (seperti /products/add.php) secara langsung via address bar.
   - Hasil: Sistem memblokir akses dan mengalihkan pengguna kembali ke halaman utama Dashboard.

## 5. Panduan Instalasi dan Konfigurasi

### Persyaratan Sistem
- PHP versi 8.1.10 atau yang lebih baru
- Web Server Laragon
- Database Server MySQL / MariaDB

### Langkah Instalasi
1. Clone repository ke folder root web server:
   git clone https://github.com/Arstaaw1/inventory-app.git
2. Import database:
   - Buat database baru pada phpMyAdmin dengan nama `inventory_db`.
   - Import file `schema.sql` yang berada di direktori utama proyek ini.
3. Konfigurasi koneksi database:
   - Sesuaikan konfigurasi host, port, username, dan password database pada file `config/database.php`.
4. Akun Default Pengujian:
   - Administrator : username `admin` | password `password123`
   - Staff         : username `staff` | password `password123`