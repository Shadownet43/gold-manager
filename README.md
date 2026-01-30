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

Isi `.env` untuk database dan (opsional) mail Gmail untuk verifikasi/lupa password:

- `DB_*` — koneksi database
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

## Deploy ke Vercel + TiDB Cloud

### 1. Setup TiDB Cloud (Database)

1. Buka [TiDB Cloud](https://tidbcloud.com/) dan buat akun gratis
2. Buat cluster baru (pilih **Serverless** untuk free tier)
3. Setelah cluster aktif, buka **Connect** dan catat:
   - Host (contoh: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
   - Port (biasanya `4000`)
   - Username
   - Password
4. Buat database baru via SQL Editor:
   ```sql
   CREATE DATABASE gold_tracker;
   ```

### 2. Setup Vercel

1. Push repo ke GitHub (jika belum)
2. Buka [Vercel](https://vercel.com/) dan import repo
3. Tambahkan Environment Variables di Vercel:

   | Variable | Value |
   |----------|-------|
   | `APP_NAME` | WoW Gold Tracker |
   | `APP_ENV` | production |
   | `APP_KEY` | (generate dengan `php artisan key:generate --show`) |
   | `APP_DEBUG` | false |
   | `APP_URL` | https://your-app.vercel.app |
   | `DB_CONNECTION` | mysql |
   | `DB_HOST` | (dari TiDB Cloud) |
   | `DB_PORT` | 4000 |
   | `DB_DATABASE` | gold_tracker |
   | `DB_USERNAME` | (dari TiDB Cloud) |
   | `DB_PASSWORD` | (dari TiDB Cloud) |
   | `SESSION_DRIVER` | cookie |
   | `SESSION_ENCRYPT` | true |
   | `CACHE_STORE` | array |
   | `MAIL_MAILER` | smtp |
   | `MAIL_HOST` | smtp.gmail.com |
   | `MAIL_PORT` | 587 |
   | `MAIL_USERNAME` | (Gmail Anda) |
   | `MAIL_PASSWORD` | (Google App Password) |
   | `MAIL_ENCRYPTION` | tls |

4. Deploy!

### 3. Jalankan Migrasi

Setelah deploy, jalankan migrasi database:

**Opsi A:** Via Vercel CLI
```bash
vercel env pull .env.production
php artisan migrate --env=production --force
```

**Opsi B:** Via TiDB Cloud SQL Editor
- Export SQL dari lokal: `php artisan schema:dump`
- Jalankan SQL di TiDB Cloud SQL Editor

---

## Resend: Verifikasi Domain (agar bisa kirim ke semua email)

Akun Resend dalam **mode testing** hanya bisa mengirim ke email pemilik akun. Untuk kirim verifikasi ke email user lain:

1. Buka [resend.com/domains](https://resend.com/domains)
2. Klik **Add Domain** dan masukkan domain Anda (mis. `gold-tracker.com`)
3. Tambahkan record DNS yang diminta Resend (MX, TXT, dll.)
4. Setelah domain **Verified**, di Railway set:
   - `MAIL_FROM_ADDRESS` = `noreply@domainanda.com` (atau subdomain yang Anda verifikasi)
   - `MAIL_FROM_NAME` = WoW Gold Tracker
5. Redeploy

Sementara belum punya domain: gunakan email pemilik akun Resend saat register/ubah email untuk testing.

---

## Environment Variables

| Variable | Deskripsi | Default |
|----------|-----------|---------|
| `DB_CONNECTION` | Driver database | sqlite |
| `SESSION_DRIVER` | Session storage | database |
| `CACHE_STORE` | Cache storage | database |
| `MAIL_MAILER` | resend (production) / smtp / log | log |
| `RESEND_API_KEY` | API key dari Resend | - |
| `MAIL_FROM_ADDRESS` | onboarding@resend.dev (testing) atau email domain terverifikasi | - |

---

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Database:** SQLite (lokal) / TiDB Cloud (production)
- **Frontend:** Bootstrap 5, Chart.js, SweetAlert2
- **Hosting:** Vercel (Serverless PHP)

---

## License

MIT
