<?php

namespace App\Support;

use Throwable;

class ResendMailHelper
{
    /**
     * Pesan error yang ramah user untuk kegagalan kirim email (termasuk batasan Resend testing mode).
     */
    public static function getUserMessage(Throwable $e): string
    {
        $msg = $e->getMessage();

        // Resend: mode testing hanya bisa kirim ke email pemilik akun
        if (str_contains($msg, 'only send testing emails to your own email')
            || str_contains($msg, 'verify a domain')
            || str_contains($msg, 'resend.com/domains')) {
            return 'Resend dalam mode testing: verifikasi email hanya bisa dikirim ke email pemilik akun Resend. '
                . 'Untuk kirim ke email lain, verifikasi domain di https://resend.com/domains lalu set MAIL_FROM_ADDRESS ke email domain Anda (mis. noreply@domainanda.com).';
        }

        // API key invalid / missing
        if (str_contains($msg, 'invalid_api_key') || str_contains($msg, 'missing_api_key') || str_contains($msg, 'API key')) {
            return 'RESEND_API_KEY tidak valid atau belum di-set di Railway. Periksa di Resend Dashboard → API Keys.';
        }

        // Generic
        return 'Pengiriman email gagal. Pastikan MAIL_MAILER=resend, RESEND_API_KEY, dan MAIL_FROM_ADDRESS sudah benar di Railway.';
    }
}
