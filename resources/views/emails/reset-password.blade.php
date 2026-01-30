<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; padding: 40px 20px; }
        .wrapper { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 24px; text-align: center; }
        .logo { font-size: 28px; font-weight: 700; color: #fbbf24; letter-spacing: -0.5px; }
        .logo span { color: #94a3b8; font-size: 14px; font-weight: 500; display: block; margin-top: 4px; }
        .body { padding: 32px 28px; color: #334155; line-height: 1.6; }
        .greeting { font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; color: #475569; margin-bottom: 24px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #000 !important; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4); }
        .muted { font-size: 13px; color: #94a3b8; margin-top: 24px; }
        .expire { font-size: 13px; color: #64748b; background: #f1f5f9; padding: 10px 14px; border-radius: 8px; margin-top: 16px; }
        .footer { background: #f8fafc; padding: 20px 28px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo">WoW Gold RMT Tracker</div>
            <span>Pencatatan gold & penjualan</span>
        </div>
        <div class="body">
            <div class="greeting">Permintaan Reset Password</div>
            <p class="text">Kami menerima permintaan untuk mengatur ulang password akun Anda. Klik tombol di bawah untuk <strong>memilih password baru</strong>.</p>
            <div class="btn-wrap">
                <a href="{{ $url }}" class="btn">Atur Ulang Password</a>
            </div>
            <p class="expire">Link ini berlaku selama {{ $expire }} menit. Jangan bagikan link ini kepada siapa pun.</p>
            <p class="muted">Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tetap aman.</p>
        </div>
        <div class="footer">
            Email ini dikirim otomatis dari <strong>WoW Gold RMT Tracker</strong>. Jangan balas ke email ini.
        </div>
    </div>
</body>
</html>
