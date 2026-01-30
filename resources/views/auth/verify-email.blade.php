<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - WoW Gold RMT Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; }
        .card-header { border-bottom: 1px solid #334155; color: #fbbf24; font-weight: bold; }
        .text-gold { color: #fbbf24 !important; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 480px;">
        <div class="text-center mb-4">
            <i class="fas fa-envelope-circle-check text-gold" style="font-size: 3rem;"></i>
            <h4 class="text-white mt-2">Verifikasi Email</h4>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><i class="fas fa-envelope me-2"></i>Periksa Email Anda</div>
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <p class="text-secondary mb-4">Link verifikasi telah dikirim ke <strong class="text-white">{{ Auth::user()->email }}</strong>. Klik link di email untuk memverifikasi akun.</p>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Ulang Link Verifikasi</button>
                </form>
                <hr class="border-secondary my-3">
                <p class="text-center text-secondary small mb-0">
                    <a href="{{ route('profile.edit') }}" class="text-gold text-decoration-none">Profil</a> &middot;
                    <a href="{{ route('logout') }}" class="text-danger text-decoration-none" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </p>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
