<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah dipakai user lain.',
        ]);

        $user->name = $validated['name'];
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->save();

            try {
                set_time_limit(90);
                $user->sendEmailVerificationNotification();
                $status = 'Profil berhasil diperbarui. Cek email untuk link verifikasi.';
            } catch (\Throwable $e) {
                Log::warning('Email verifikasi gagal dikirim: ' . $e->getMessage(), ['exception' => $e]);
                $hint = config('mail.default') === 'resend'
                    ? 'Pastikan MAIL_MAILER=resend, RESEND_API_KEY, dan MAIL_FROM_ADDRESS=onboarding@resend.dev sudah di-set di Railway.'
                    : 'Gunakan Resend: set MAIL_MAILER=resend dan RESEND_API_KEY di Railway (tidak timeout seperti SMTP).';
                $status = 'Profil berhasil diperbarui. Pengiriman email verifikasi gagal. ' . $hint;
                if (config('app.debug')) {
                    $status .= ' [Debug: ' . $e->getMessage() . ']';
                }
            }
        } else {
            $user->save();
            $status = 'Profil berhasil diperbarui.';
        }

        return back()->with('status', $status);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }
}
