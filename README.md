# SAE Bakery - Sistem Manajemen Inventory

Sistem manajemen inventory untuk bakery & coffee shop yang dibangun dengan Laravel. Aplikasi ini membantu mengelola stok bahan baku, barang dalam proses, barang jadi, pembelian, penjualan, dan berbagai laporan keuangan.

## 📋 Daftar Isi

- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Bahasa Pemrograman](#bahasa-pemrograman)
- [Fitur Utama](#fitur-utama)
- [Persyaratan Sistem (Lokal)](#persyaratan-sistem-lokal)
- [Cara Setup di Laptop Lokal](#cara-setup-di-laptop-lokal)
- [Persyaratan Server untuk Deployment](#persyaratan-server-untuk-deployment)
- [Aplikasi yang Dibutuhkan di Server](#aplikasi-yang-dibutuhkan-di-server)
- [Struktur Database](#struktur-database)
- [User Roles](#user-roles)
- [Troubleshooting](#troubleshooting)

---

## 🛠 Teknologi yang Digunakan

### Backend Framework
- **Laravel 12.0** - PHP Framework untuk pengembangan web application
- **PHP 8.2+** - Bahasa pemrograman server-side

### Frontend
- **Tailwind CSS 4.0** - CSS Framework untuk styling
- **Vite 7.0** - Build tool dan development server
- **JavaScript (Vanilla)** - Untuk interaktivitas di client-side
- **Chart.js** - Untuk visualisasi data (di beberapa laporan)

### Database
- **MySQL/MariaDB** - Database management system

### Library & Package
- **DomPDF (barryvdh/laravel-dompdf)** - Untuk generate PDF
- **PhpSpreadsheet (maatwebsite/excel)** - Untuk export ke Excel
- **Laravel Tinker** - Command-line tool untuk Laravel

### Development Tools
- **Composer** - PHP dependency manager
- **NPM** - Node.js package manager
- **Git** - Version control system

---

## 💻 Bahasa Pemrograman

1. **PHP 8.2+** - Backend development
   - Object-Oriented Programming (OOP)
   - Eloquent ORM untuk database operations
   - Blade templating engine

2. **JavaScript** - Frontend interactivity
   - Vanilla JavaScript (tanpa framework)
   - AJAX untuk komunikasi dengan server
   - DOM manipulation

3. **SQL** - Database queries
   - MySQL/MariaDB syntax
   - Eloquent ORM (abstraction layer)

4. **HTML/CSS** - Markup dan styling
   - HTML5
   - Tailwind CSS utility classes
   - Responsive design

---

## ✨ Fitur Utama

### 1. Manajemen Master Data
- Input Data Barang (Bahan Baku, Barang Dalam Proses, Barang Jadi)
- Input Data Supplier
- Input Data Customer
- Input Saldo Awal

### 2. Transaksi Masuk (Input Barang)
- Pembelian Bahan Baku
- Barang Dalam Proses
- Barang Jadi

### 3. Transaksi Keluar (Output Barang)
- Penjualan Barang Jadi
- Pemakaian Bahan Baku
- Pemakaian Barang Dalam Proses

### 4. Laporan
- Laporan Stock Akhir
- Laporan Stock Opname
- Kartu Stock
- Laporan Pembelian Bahan Baku
- Laporan Penjualan
- Laporan Pemakaian Bahan Baku
- Laporan Pemakaian Barang Dalam Proses
- Laporan Barang Dalam Proses
- Laporan Barang Jadi
- Laporan Data Barang
- Laporan Data Customer
- Laporan Data Supplier

### 5. Export Data
- Export ke PDF (menggunakan DomPDF)
- Export ke Excel (menggunakan PhpSpreadsheet)
git 
### 6. User Management
- Multi-user system dengan 3 level role:
  - **Pimpinan (Superadmin)** - Full access
  - **Admin** - Manage karyawan dan data
  - **Karyawan** - Input transaksi saja

---

## 💾 Persyaratan Sistem (Lokal)

### Minimum Requirements untuk Laptop/PC:

#### Operating System
- **Windows 10/11** (64-bit)
- **macOS 10.15+**
- **Linux** (Ubuntu 20.04+, Debian 11+, atau distribusi modern lainnya)

#### Hardware
- **Processor**: Intel Core i3 atau AMD equivalent (minimal 2.0 GHz)
- **RAM**: Minimal 4 GB (disarankan 8 GB)
- **Storage**: Minimal 5 GB free space
- **Internet**: Koneksi internet untuk download dependencies

#### Software yang Harus Diinstall:

1. **PHP 8.2 atau lebih tinggi**
   - Extension yang diperlukan:
     - `php-mbstring`
     - `php-xml`
     - `php-curl`
     - `php-zip`
     - `php-gd`
     - `php-mysql` atau `php-mysqli`
     - `php-openssl`
     - `php-pdo`
     - `php-tokenizer`
     - `php-json`
     - `php-bcmath`

2. **Composer** (PHP Package Manager)
   - Versi terbaru
   - Download dari: https://getcomposer.org/

3. **Node.js** (versi 18.x atau 20.x)
   - Download dari: https://nodejs.org/
   - NPM akan terinstall otomatis

4. **MySQL** atau **MariaDB**
   - MySQL 8.0+ atau MariaDB 10.6+
   - Atau bisa menggunakan XAMPP/WAMP/LAMP yang sudah include MySQL

5. **Git** (opsional, tapi disarankan)
   - Download dari: https://git-scm.com/

6. **Text Editor/IDE** (opsional)
   - Visual Studio Code
   - PhpStorm
   - Sublime Text
   - Atau editor lainnya

#### Rekomendasi Setup untuk Windows:
- **XAMPP** (include Apache, MySQL, PHP)
  - Download: https://www.apachefriends.org/
  - Atau **Laragon** (lebih modern untuk Laravel)
  - Download: https://laragon.org/

#### Rekomendasi Setup untuk macOS:
- **Homebrew** untuk install PHP, MySQL, Composer
- **MAMP** atau **Valet** (Laravel development environment)

#### Rekomendasi Setup untuk Linux:
- Install via package manager (apt, yum, dll)
- Atau gunakan **Laravel Sail** (Docker)

---

## 🚀 Cara Setup di Laptop Lokal

### Langkah 1: Clone Repository
```bash
git clone <repository-url>
cd WEBDIKI-main
```

### Langkah 2: Install Dependencies

#### Install PHP Dependencies (Composer)
```bash
composer install
```

#### Install Node.js Dependencies
```bash
npm install
```

### Langkah 3: Setup Environment

1. **Copy file environment:**
```bash
cp .env.example .env
```

2. **Generate application key:**
```bash
php artisan key:generate
```

3. **Edit file `.env`** dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 4: Setup Database

1. **Buat database baru di MySQL:**
```sql
CREATE DATABASE nama_database_anda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Jalankan migration:**
```bash
php artisan migrate
```

3. **Jalankan seeder (opsional, untuk data awal):**
```bash
php artisan db:seed
```

### Langkah 5: Build Assets

```bash
npm run build
```

Atau untuk development dengan hot reload:
```bash
npm run dev
```

### Langkah 6: Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

### Langkah 7: Login

**Default User (jika sudah run seeder):**
- **Email**: `pimpinan@sea-bakery.com`
- **Password**: `Pimpinana123!`
- **Role**: Pimpinan (Superadmin)

---

## 🌐 Persyaratan Server untuk Deployment

### Minimum Server Requirements:

#### Hardware
- **CPU**: 2 Core (disarankan 4 Core)
- **RAM**: Minimal 2 GB (disarankan 4 GB)
- **Storage**: Minimal 20 GB SSD
- **Bandwidth**: Minimal 100 Mbps

#### Operating System
- **Linux** (Ubuntu 20.04 LTS atau lebih baru)
- **CentOS 8+** atau **Rocky Linux**
- **Debian 11+**

---

## 📦 Aplikasi yang Dibutuhkan di Server

### 1. Web Server
Pilih salah satu:

#### **Nginx** (Disarankan)
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install nginx

# CentOS/Rocky Linux
sudo yum install nginx
```

#### **Apache** (Alternatif)
```bash
# Ubuntu/Debian
sudo apt install apache2

# CentOS/Rocky Linux
sudo yum install httpd
```

### 2. PHP 8.2+ dengan Extensions
```bash
# Ubuntu/Debian
sudo apt install php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring \
    php8.2-curl php8.2-xml php8.2-bcmath php8.2-openssl \
    php8.2-tokenizer php8.2-json

# CentOS/Rocky Linux
sudo yum install php82-php-fpm php82-php-cli php82-php-common \
    php82-php-mysqlnd php82-php-zip php82-php-gd php82-php-mbstring \
    php82-php-curl php82-php-xml php82-php-bcmath php82-php-openssl \
    php82-php-json
```

### 3. MySQL atau MariaDB
```bash
# MySQL (Ubuntu/Debian)
sudo apt install mysql-server

# MariaDB (Alternatif)
sudo apt install mariadb-server mariadb-client

# CentOS/Rocky Linux
sudo yum install mysql-server
# atau
sudo yum install mariadb-server mariadb
```

### 4. Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 5. Node.js dan NPM
```bash
# Menggunakan NodeSource (Ubuntu/Debian)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# CentOS/Rocky Linux
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo yum install -y nodejs
```

### 6. Git
```bash
# Ubuntu/Debian
sudo apt install git

# CentOS/Rocky Linux
sudo yum install git
```

### 7. SSL Certificate (Opsional, tapi disarankan)
- **Let's Encrypt** (gratis)
```bash
sudo apt install certbot python3-certbot-nginx
# atau untuk Apache
sudo apt install certbot python3-certbot-apache
```

### 8. Process Manager (Opsional, untuk production)
- **Supervisor** - untuk manage queue workers
```bash
sudo apt install supervisor
```

- **PM2** - alternatif untuk Node.js processes
```bash
sudo npm install -g pm2
```

---

## 🔧 Konfigurasi Server untuk Deployment

### 1. Nginx Configuration

Buat file konfigurasi di `/etc/nginx/sites-available/sae-bakery`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/sae-bakery/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi:
```bash
sudo ln -s /etc/nginx/sites-available/sae-bakery /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 2. Set Permissions

```bash
cd /var/www/sae-bakery
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Optimize Laravel untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 4. Setup Queue Worker (jika menggunakan queue)

Edit `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sae-bakery/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sae-bakery/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

---

## 🗄 Struktur Database

Aplikasi menggunakan database MySQL/MariaDB dengan tabel-tabel berikut:

- `users` - Data user dan authentication
- `barangs` - Master data barang
- `suppliers` - Data supplier
- `customers` - Data customer
- `pembelian_bahan_bakus` - Header pembelian
- `pembelian_bahan_baku_details` - Detail pembelian
- `pemakaian_bahan_bakus` - Header pemakaian bahan baku
- `pemakaian_bahan_baku_details` - Detail pemakaian
- `barang_dalam_proses` - Header barang dalam proses
- `barang_dalam_proses_details` - Detail barang dalam proses
- `pemakaian_barang_dalam_proses` - Header pemakaian barang dalam proses
- `pemakaian_barang_dalam_proses_details` - Detail pemakaian
- `barang_jadis` - Header barang jadi
- `barang_jadi_details` - Detail barang jadi
- `penjualan_barang_jadis` - Header penjualan
- `penjualan_barang_jadi_details` - Detail penjualan

---

## 👥 User Roles

### 1. Pimpinan (Superadmin)
- Full access ke semua fitur
- Bisa menambah/edit/hapus semua user
- Bisa menghapus semua transaksi
- Bisa mengakses semua laporan

### 2. Admin
- Bisa mengelola master data (barang, supplier, customer)
- Bisa menambah/edit/hapus user karyawan
- Bisa menghapus beberapa transaksi
- Bisa mengakses semua laporan

### 3. Karyawan
- Hanya bisa input transaksi (masuk dan keluar)
- Tidak bisa mengakses laporan
- Tidak bisa mengelola master data
- Tidak bisa mengelola user

---

## 🔍 Troubleshooting

### Masalah: Composer install error
**Solusi:**
```bash
composer clear-cache
composer install --no-cache
```

### Masalah: Permission denied pada storage
**Solusi:**
```bash
chmod -R 775 storage bootstrap/cache
```

### Masalah: Database connection error
**Solusi:**
- Pastikan MySQL/MariaDB sudah running
- Cek konfigurasi di file `.env`
- Pastikan database sudah dibuat
- Cek username dan password database

### Masalah: npm install error
**Solusi:**
```bash
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Masalah: Vite build error
**Solusi:**
```bash
npm run build
# atau untuk development
npm run dev
```

### Masalah: 500 Internal Server Error
**Solusi:**
- Cek file `.env` sudah benar
- Jalankan `php artisan config:clear`
- Cek permission folder `storage` dan `bootstrap/cache`
- Cek log error di `storage/logs/laravel.log`

---

## 📞 Support

Untuk pertanyaan atau bantuan, silakan hubungi tim development.

---

## 📄 License

Aplikasi ini dibuat untuk internal use SAE Bakery.

---

**Last Updated**: November 2025

