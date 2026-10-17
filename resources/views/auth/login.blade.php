<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Kantin Ibnunnafis Depok</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('no wat.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('no wat.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('no wat.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="login-visual">
            <img src="{{ asset('8742-removebg.png') }}" alt="Aktivitas Kantin Ibnunnafis">
        </div>

        <div class="login-form-panel">
            <div class="text-center mb-4">
                <div class="login-logo">
                    <img src="{{ asset('no wat.png') }}" alt="Logo Kantin Ibnunnafis">
                </div>
                <h2 class="fw-bold text-slate-900 mb-1 fs-4" style="font-family: var(--font-heading);">Kantin Ibnunnafis</h2>
                <p class="text-muted text-sm mb-0">Sistem Informasi Penjualan & Persediaan</p>
            </div>

        @if(session('info'))
        <div class="alert alert-info text-sm py-2 px-3 mb-3 d-flex align-items-center gap-2">
            <iconify-icon icon="lucide:info"></iconify-icon>
            <div>{{ session('info') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger text-sm py-2 px-3 mb-3 d-flex align-items-center gap-2">
            <iconify-icon icon="lucide:alert-circle"></iconify-icon>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-xs text-uppercase text-muted">ID Pengguna</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0 ps-3 pe-2">
                        <iconify-icon icon="lucide:user" class="fs-5"></iconify-icon>
                    </span>
                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10" name="username" id="username" class="form-control border-start-0 ps-2 tabular-nums @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: 12345" autocomplete="off" required autofocus style="letter-spacing: 0.05em; font-weight: 600;">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-xs text-uppercase text-muted">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0 ps-3 pe-2">
                        <iconify-icon icon="lucide:lock" class="fs-5"></iconify-icon>
                    </span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 ps-2 @error('password') is-invalid @enderror" value="" placeholder="•••••" autocomplete="off" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:log-in"></iconify-icon>
                Masuk
            </button>
        </form>
        </div>
    </main>
</body>
</html>