<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - WoW Gold RMT Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; }
        .card-header { border-bottom: 1px solid #334155; color: #fbbf24; font-weight: bold; }
        .form-control { background-color: #0f172a; border-color: #334155; color: #fff; }
        .form-control:focus { background-color: #0f172a; border-color: #fbbf24; color: #fff; box-shadow: none; }
        .text-gold { color: #fbbf24 !important; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <div class="text-center mb-4">
            <i class="fas fa-coins text-gold" style="font-size: 3rem;"></i>
            <h4 class="text-white mt-2">WoW Gold RMT Tracker</h4>
            <p class="text-secondary small">Pencatatan pribadi gold & penjualan</p>
        </div>
        <div class="card shadow">
            <div class="card-header py-3">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $err) {{ $err }} @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="email@contoh.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        <div class="mt-1">
                            <a href="{{ route('password.request') }}" class="text-gold small text-decoration-none">Lupa password?</a>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-secondary small" for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>
                <hr class="border-secondary my-3">
                <p class="text-center text-secondary small mb-0">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-gold text-decoration-none">Daftar</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
