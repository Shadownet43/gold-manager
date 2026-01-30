# WoW Gold RMT Tracker

Aplikasi pencatatan pribadi untuk gold & penjualan RMT (WoW). Dibuat dengan Laravel.

## Fitur

- Dashboard transaksi gold (jual, riwayat, grafik)
- Stok gold & target stok
- Kalkulator AH (5%)
- Multi-user: login, register, profil
- Verifikasi email & lupa password
- Edit profil & ganti password

## Instalasi

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

## License

MIT
