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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
}
