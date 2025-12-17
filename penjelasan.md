# Penjelasan Teknis Aplikasi Inventory SAE Bakery

Dokumen ini berisi penjelasan mendalam dan lengkap mengenai teknis, fitur, dan alur kerja aplikasi Inventory SAE Bakery.

## 🚀 Gambaran Umum (High-Level Overview)

**SAE Bakery Inventory System** adalah aplikasi berbasis web yang dirancang khusus untuk menangani kompleksitas manajemen stok di industri roti dan kedai kopi.

Berbeda dengan aplikasi kasir biasa, sistem ini menangani **tiga tahap siklus produksi**:
1.  **Bahan Baku** (Tepung, Gula, dll.) - Dibeli dari supplier.
2.  **Barang Dalam Proses** (Adonan, Setengah Jadi) - Hasil olahan bahan baku.
3.  **Barang Jadi** (Roti, Kue) - Siap dijual ke customer.

Masalah utama yang diselesaikan aplikasi ini adalah **ketidakakuratan HPP (Harga Pokok Penjualan)**. Dengan sistem manual, sulit mengetahui berapa modal pasti untuk satu roti jika harga tepung berubah-ubah. Aplikasi ini menggunakan metode penilaian stok (FIFO/LIFO/Average) untuk menghitung HPP secara otomatis dan presisi setiap kali terjadi penjualan.

---

## 🛠 Tech Stack & Dependencies

Aplikasi ini dibangun menggunakan teknologi modern yang stabil dan scalable.

### Core Languages
*   **PHP 8.2+**: Bahasa pemrograman utama backend.
*   **JavaScript (ES6+)**: Interaktivitas frontend.
*   **HTML5 & CSS3**: Struktur dan styling.

### Frameworks & Libraries
*   **Laravel 12.0**: Framework PHP full-stack yang kuat untuk keamanan, routing, dan ORM.
*   **Tailwind CSS 4.0**: Framework CSS utility-first untuk desain UI yang cepat dan responsif.
*   **Vite 7.0**: Build tool generasi terbaru untuk aset frontend yang sangat cepat.
*   **Blade Templates**: Engine templating bawaan Laravel.

### Database & Storage
*   **MySQL / MariaDB**: Database relasional utama (Production).
*   **SQLite**: Database ringan untuk testing dan development.
*   **Eloquent ORM**: Layer abstraksi database Laravel untuk interaksi data yang aman dan ekspresif.

### External Libraries (Dependencies)
*   `barryvdh/laravel-dompdf`: Library untuk mencetak laporan ke format PDF (Kartu Stok, Invoice).
*   `maatwebsite/excel`: Library untuk ekspor data laporan ke Excel (.xlsx).
*   `concurrently`: Utility untuk menjalankan server backend dan frontend secara bersamaan saat development.

---

## ✨ Fitur Unggulan (Key Features)

1.  **Triple-Layer Inventory Management**
    Sistem memisahkan stok menjadi tiga kategori independen (Bahan Baku, WIP, Barang Jadi) namun saling terhubung melalui transaksi konversi (Pemakaian), mencerminkan alur produksi pabrik roti yang sesungguhnya.

2.  **Penilaian Stok Otomatis (FIFO/LIFO/Average)**
    Fitur paling krusial. Sistem tidak hanya mencatat jumlah, tapi juga **nilai** dari setiap batch barang.
    *   *Contoh:* Jika beli tepung Batch A @10rb dan Batch B @12rb. Saat pakai tepung, sistem otomatis mengambil Batch A dulu (FIFO), sehingga perhitungan laba rugi sangat akurat.

3.  **Laporan Real-Time & Kartu Stok**
    Setiap pergerakan barang (masuk/keluar) tercatat detik itu juga. Admin bisa menarik "Kartu Stok" untuk melihat riwayat audit lengkap: kapan barang masuk, siapa yang input, dan berapa sisa saldo per transaksi.

4.  **Role-Based Access Control (RBAC) Berjenjang**
    Keamanan data terjamin dengan pembagian hak akses:
    *   **Pimpinan:** Akses penuh & hapus data sensitif.
    *   **Admin:** Kelola master data.
    *   **Karyawan:** Hanya input transaksi operasional.

---

## 🔍 Detail Fitur & Navigasi

Berikut adalah bedah fitur lengkap berdasarkan menu aplikasi:

### 1. Dashboard
*   **Statistik Utama:** Total Item, Nilai Barang Masuk, Nilai Barang Keluar (dalam Rupiah).
*   **Grafik Tren:** Visualisasi jumlah transaksi selama 30 hari terakhir.
*   **Shortcut Opname:** Menampilkan jika ada selisih stok yang perlu ditinjau.

### 2. Sidebar Menu

#### A. Input Data (Master Data)
*Hanya dapat diakses oleh Admin & Superadmin*
*   **Input Data Barang:** Menambah item baru. Sistem otomatis generate Kode Barang unik.
*   **Input Data Supplier:** Database pemasok bahan baku.
*   **Input Data Customer:** Database pelanggan (termasuk pelanggan retail/umum).
*   **Input Saldo Awal:** Menu krusial untuk inisialisasi stok awal sistem. Ini adalah **satu-satunya** tempat di mana harga dasar (`Harga Beli` & `Harga Jual`) ditentukan secara manual.

#### B. Input Barang Masuk (Inbound)
*Dapat diakses Karyawan, Admin, Superadmin*
*   **Pembelian Bahan Baku:** Transaksi beli dari supplier. Menambah stok Bahan Baku. Validasi ketat: hanya item kategori "Bahan Baku" yang muncul.
*   **Barang Dalam Proses:** Mencatat hasil olahan (misal: tepung jadi adonan). Menambah stok WIP.
*   **Barang Jadi:** Mencatat hasil produksi akhir (misal: adonan jadi Roti Tawar). Menambah stok Barang Jadi.

#### C. Input Barang Keluar (Outbound)
*Dapat diakses Karyawan, Admin, Superadmin*
*   **Penjualan Barang Jadi:** Kasir penjualan. Mengurangi stok Barang Jadi dan menghitung profit.
*   **Pemakaian Bahan Baku:** Mengambil bahan dari gudang untuk produksi. Mengurangi stok Bahan Baku.
*   **Pemakaian Barang Dalam Proses:** Mengambil stok WIP untuk tahap finishing.

#### D. Laporan (Reports)
*Hanya Admin & Superadmin*
*   **Laporan Pembelian & Penjualan:** Rekap transaksi per periode.
*   **Laporan Pemakaian:** Analisis penggunaan bahan.
*   **Laporan Data Master:** Daftar Barang/Supplier/Customer.
*   **Kartu Stock (Stock Card):** Laporan audit trail per item. Menampilkan saldo awal, masuk, keluar, dan saldo akhir per transaksi.
*   **Laporan Stock Akhir:** Valuasi total aset persediaan saat ini (Total Qty * Nilai Rata-rata/Batch).
*   **Laporan Stock Opname:** Hasil cek fisik vs sistem.

#### E. Manajemen User
*   **Kelola User:** Tambah/Edit/Hapus akun login. Admin hanya bisa kelola Karyawan, Superadmin bisa kelola semua.

---

## ⚙️ Logika Sistem & Alur Kerja (Workflows)

Bagian ini menjelaskan "otak" di balik layar aplikasi.

### 1. Sistem "Inventory Batch" (Batch Tracking)
Alih-alih hanya menyimpan kolom `stok` (integer) di tabel Barang, sistem menggunakan tabel terpisah bernama `inventory_batches`.
*   **Saat Input (Masuk):** Sistem membuat *record* baru di `inventory_batches` berisi: `barang_id`, `qty_awal`, `qty_sisa`, `harga_beli` (harga saat itu), dan `tanggal`.
*   **Saat Output (Keluar):** `InventoryService` akan mencari batch mana yang harus dikurangi stoknya berdasarkan metode yang dipilih (FIFO/LIFO).
    *   *Algoritma FIFO:* Cari batch dengan `qty_sisa > 0` yang `tanggal`-nya paling lama (ascending).
    *   *Algoritma LIFO:* Cari batch dengan `qty_sisa > 0` yang `tanggal`-nya paling baru (descending).

### 2. Otoritas Harga (Pricing Authority)
Untuk mencegah manipulasi atau kesalahan input oleh karyawan:
*   Harga di form transaksi (kecuali Saldo Awal) bersifat **Read-Only** atau otomatis diambil dari Master Data.
*   Karyawan tidak bisa mengubah harga beli saat transaksi pembelian; harga harus diupdate oleh Admin di Master Data terlebih dahulu jika ada kenaikan harga permanen. Ini menjaga standar *Standard Costing*.

### 3. Smart Deletion & Integritas Data
Menghapus data di aplikasi inventori sangat berisiko merusak saldo. Oleh karena itu, logika penghapusan di `ReportController` sangat ketat:
*   **Hapus Transaksi MASUK:** Sistem mengecek apakah stok dari transaksi tersebut sudah terpakai? Jika batch dari transaksi tersebut sisa stoknya < stok awal (artinya sudah ada yang terpakai), penghapusan **DITOLAK** untuk mencegah stok menjadi minus atau data menggantung.
*   **Hapus Transaksi KELUAR:** Sistem akan **MENGEMBALIKAN** stok ke batch asalnya. Batch yang tadinya `qty_sisa`-nya 0 akan bertambah kembali.

### 4. Autentikasi & Keamanan
*   Menggunakan Laravel Auth standar.
*   Middleware `CheckRole` memvalidasi setiap request. Jika user mencoba akses URL `/users` tapi role-nya `employee`, sistem langsung memblokir akses (403 Forbidden).
*   API route untuk AJAX (seperti pencarian barang) dilindungi middleware `auth` sehingga tidak bisa diakses publik.

---

## 💻 Panduan Instalasi & Setup

Ikuti langkah ini untuk menjalankan aplikasi di komputer lokal (Windows/Mac/Linux).

### Prasyarat
Pastikan sudah terinstall:
1.  **PHP 8.2+** (Cek dengan `php -v`)
2.  **Composer** (Cek dengan `composer -v`)
3.  **Node.js & NPM** (Cek dengan `node -v`)
4.  **Database** (MySQL atau SQLite)

### Langkah-langkah

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/sae-bakery.git
    cd sae-bakery
    ```

2.  **Install Vendor PHP**
    ```bash
    composer install
    ```

3.  **Install Vendor Frontend**
    ```bash
    npm install
    ```

4.  **Setup Environment**
    Duplikat file konfigurasi contoh:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan atur koneksi database (jika pakai MySQL):
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=sae_bakery
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    *(Jika pakai SQLite, cukup biarkan default atau hapus konfigurasi DB_HOST dll)*

5.  **Generate Key & Migrasi**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
    *Perintah `--seed` akan otomatis membuat user default: Superadmin, Admin, dan Karyawan.*

6.  **Jalankan Aplikasi**
    Gunakan satu perintah ini untuk menjalankan Laravel Server + Vite Hot Reload sekaligus:
    ```bash
    npm run dev
    ```
    Atau secara manual di 2 terminal berbeda:
    *   Terminal 1: `php artisan serve`
    *   Terminal 2: `npm run dev`

7.  **Akses Browser**
    Buka `http://localhost:8000`.
    *   **Login Superadmin:** `pimpinan@sea-bakery.com` / `Pimpinana123!` (Sesuaikan dengan `DatabaseSeeder.php` jika berbeda).

---
*Dokumen ini dibuat secara otomatis oleh Bolt ⚡ untuk referensi pengembangan dan penggunaan.*
