# WoW Gold RMT Tracker

Aplikasi pencatatan pribadi untuk gold & penjualan RMT (WoW). Dibuat dengan Laravel 12.

## Fitur

- Dashboard transaksi gold (jual, riwayat, grafik)
- Stok gold & target stok
- Kalkulator AH (5%)
- Multi-user: login, register, profil
- Verifikasi email & lupa password
- Edit profil & ganti password

---

## Instalasi Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Isi `.env` untuk database dan (opsional) mail Gmail:

- `DB_*` — koneksi database (MySQL atau SQLite)
- `MAIL_USERNAME` — Gmail Anda
- `MAIL_PASSWORD` — Google App Password
- `APP_URL` — URL aplikasi (mis. `http://127.0.0.1:8000`)

```bash
php artisan migrate
php artisan db:seed --class=SettingSeeder
php artisan serve
```

Buka `/register` untuk buat akun pertama.

---

## Deploy ke Hosting Sendiri (cPanel / Shared Hosting)

### Persyaratan

- PHP 8.2+
- MySQL 5.7+ atau MariaDB
- Composer (untuk build di lokal, lalu upload)
- Ekstensi PHP: mbstring, openssl, PDO, tokenizer, xml, ctype, json, bcmath, fileinfo

### 1. Build di komputer lokal

```bash
composer install --optimize-autoloader --no-dev
php artisan key:generate --show
# Catat APP_KEY yang muncul
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Buat database di hosting

- Login cPanel → MySQL Databases
- Buat database baru (mis. `username_gold`)
- Buat user MySQL dan beri akses ke database tersebut
- Catat: nama database, username, password

### 3. Upload file ke hosting

- Upload **seluruh isi folder project** ke hosting (FTP/File Manager), **kecuali**:
  - `node_modules/`
  - `.env` (akan dibuat manual di server)
  - `.git/`
- Pastikan struktur folder tetap (app, config, public, routes, dll.)

### 4. Set document root ke folder `public`

- Di cPanel: **Domains** → **Domains** → pilih domain → **Document Root**
- Set ke: `public_html/gold-manager/public` (sesuaikan path jika beda)
- Atau pindahkan isi `public/*` ke `public_html/` dan sesuaikan path di `index.php` (biasanya hosting punya panduan Laravel)

### 5. Buat file `.env` di server

Copy dari `.env.example`, lalu isi:

```env
APP_NAME="WoW Gold Tracker"
APP_ENV=production
APP_KEY=base64:xxxxx   # dari langkah 1
APP_DEBUG=false
APP_URL=https://domainanda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_gold
DB_USERNAME=username_db
DB_PASSWORD=password_db

SESSION_DRIVER=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_google_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
MAIL_FROM_NAME="${APP_NAME}"
```

(Ganti `domainanda.com`, kredensial DB, dan mail sesuai hosting/domain Anda.)

### 6. Jalankan migrasi di server

Via SSH (jika tersedia):

```bash
cd /path/ke/project
php artisan migrate --force
php artisan db:seed --class=SettingSeeder --force
```

Atau via **Terminal** di cPanel (jika ada), atau import SQL hasil `php artisan schema:dump` dari lokal ke phpMyAdmin.

### 7. Permission folder

Pastikan folder berikut bisa ditulis (755 atau 775):

- `storage/`
- `bootstrap/cache/`

Biasanya: `chmod -R 775 storage bootstrap/cache`

---

## Environment Variables (Hosting)

| Variable       | Deskripsi                    | Contoh                    |
|----------------|------------------------------|---------------------------|
| `APP_URL`      | URL lengkap situs            | `https://domainanda.com`  |
| `DB_*`         | Koneksi MySQL dari cPanel   | -                         |
| `MAIL_*`       | SMTP (Gmail atau SMTP hosting) | -                      |
| `SESSION_DRIVER` | `database` atau `file`     | database                  |

---

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5, Chart.js, SweetAlert2

---

## License

MIT
