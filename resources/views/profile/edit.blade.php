<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil - WoW Gold RMT Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; font-family: 'Segoe UI', sans-serif; }
        .card { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; margin-bottom: 1rem; }
        .card-header { border-bottom: 1px solid #334155; font-weight: bold; color: #fbbf24; }
        .form-control { background-color: #0f172a; border-color: #334155; color: #fff; }
        .form-control:focus { background-color: #0f172a; border-color: #fbbf24; color: #fff; box-shadow: none; }
        .text-gold { color: #fbbf24 !important; }
        .nav-pills .nav-link { background: #334155; color: #94a3b8; border-radius: 8px; margin-right: 0.5rem; }
        .nav-pills .nav-link.active { background: #fbbf24; color: #000; }
        .nav-pills .nav-link:hover { background: #475569; color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="fas fa-coins text-gold me-2"></i>WoW Gold RMT
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-home me-1"></i>Dashboard</a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                    <li><a class="dropdown-item active" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger small"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container" style="max-width: 600px;">
    <h4 class="text-white mb-4"><i class="fas fa-user-cog me-2 text-gold"></i>Profil</h4>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
        </div>
    @endif

    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="pill" href="#profil">Edit Profil</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#password">Ganti Password</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="profil">
            <div class="card">
                <div class="card-header"><i class="fas fa-user me-2"></i>Data Profil</div>
                <div class="card-body">
                    <p class="text-secondary small mb-3">Email terverifikasi: 
                        @if ($user->hasVerifiedEmail())
                            <span class="text-success"><i class="fas fa-check-circle"></i> Ya</span>
                        @else
                            <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Belum</span>
                            <a href="{{ route('verification.notice') }}" class="ms-2 text-gold small">Verifikasi sekarang</a>
                        @endif
                    </p>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            <small class="text-secondary">Jika diubah, verifikasi email baru akan dikirim.</small>
                        </div>
                        <button type="submit" class="btn btn-warning fw-bold">Simpan Profil</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password">
            <div class="card">
                <div class="card-header"><i class="fas fa-lock me-2"></i>Ganti Password</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Password Baru (min 6 karakter)</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-warning fw-bold">Ganti Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
