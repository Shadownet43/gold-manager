<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset password user (untuk login yang lupa / salah)
Artisan::command('gold:reset-password {email : Email user} {password : Password baru}', function () {
    $email = trim(strtolower($this->argument('email')));
    $password = $this->argument('password');

    $user = User::where('email', $email)->first();
    if (!$user) {
        $this->error("User dengan email '{$email}' tidak ditemukan.");
        $this->line('Cek daftar user: php artisan gold:users');
        return 1;
    }

    $user->password = Hash::make($password);
    $user->save();

    $this->info("Password untuk {$user->name} ({$email}) berhasil direset.");
    $this->info('Silakan login dengan email dan password baru.');
    return 0;
})->purpose('Reset password user agar bisa login');

// Daftar user (untuk cek email yang terdaftar)
Artisan::command('gold:users', function () {
    $users = User::all(['id', 'name', 'email']);
    if ($users->isEmpty()) {
        $this->warn('Belum ada user. Daftar lewat /register');
        return 0;
    }
    $this->table(['ID', 'Nama', 'Email'], $users->map(fn ($u) => [$u->id, $u->name, $u->email]));
    $this->line('Reset password: php artisan gold:reset-password <email> <password_baru>');
    return 0;
})->purpose('Tampilkan daftar user (email untuk login)');
