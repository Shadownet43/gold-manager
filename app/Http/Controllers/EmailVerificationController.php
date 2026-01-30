<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect('/');
        }
        return view('auth.verify-email');
    }

    public function verify(Request $request, string $id, string $hash)
    {
        $user = \App\Models\User::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('status', 'Email sudah terverifikasi.');
        }
        $user->markEmailAsVerified();
        return redirect('/')->with('status', 'Email berhasil diverifikasi!');
    }

    public function resend(Request $request)
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect('/');
        }
        Auth::user()->sendEmailVerificationNotification();
        return back()->with('status', 'Link verifikasi baru telah dikirim ke email Anda.');
    }
}
