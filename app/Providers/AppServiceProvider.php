<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tidak ada yang perlu di-register
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vercel Serverless: Setup paths di /tmp
        if (isset($_ENV['VERCEL']) || env('VERCEL')) {
            $this->setupVercelPaths();
        }
        VerifyEmail::toMailUsing(function ($notifiable, $verificationUrl) {
            return (new MailMessage)
                ->subject('Verifikasi Email - WoW Gold RMT Tracker')
                ->view('emails.verify-email', [
                    'url' => $verificationUrl,
                    'user' => $notifiable,
                ]);
        });

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);
            return (new MailMessage)
                ->subject('Reset Password - WoW Gold RMT Tracker')
                ->view('emails.reset-password', [
                    'url' => $url,
                    'user' => $notifiable,
                    'expire' => $expire,
                ]);
        });
    }

    /**
     * Setup paths untuk Vercel serverless environment
     */
    protected function setupVercelPaths(): void
    {
        // Buat direktori yang diperlukan di /tmp
        $directories = ['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }

        // Set config untuk menggunakan /tmp
        config([
            'view.compiled' => '/tmp/views',
            'cache.stores.file.path' => '/tmp/cache',
            'session.files' => '/tmp/sessions',
            'logging.channels.single.path' => '/tmp/logs/laravel.log',
        ]);
    }
}
