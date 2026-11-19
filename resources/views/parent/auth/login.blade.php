<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Portal | Sta. Lucia Academy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#003366; --primary-light:#00509e; --accent:#007bff; --accent-light:#e6f2ff; --text-dark:#1e293b; --text-muted:#64748b; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%); display:flex; align-items:center; justify-content:center; padding:1.5rem; margin:0; }
        .login-card { background:#fff; border-radius:24px; box-shadow:0 24px 64px rgba(0,20,50,.3); max-width:460px; width:100%; padding:2.5rem; }
        .school-logo { width:72px;height:72px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 8px 24px rgba(0,80,158,.2);overflow:hidden;border:2px solid var(--accent-light); }
        .school-logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .portal-badge { display:inline-flex;align-items:center;gap:.4rem;background:var(--accent-light);color:var(--primary-light);font-size:.72rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;padding:.3rem .85rem;border-radius:20px;margin:.5rem auto 1.5rem; }
        .btn-login { background:linear-gradient(135deg,var(--primary-light),var(--primary));border:none;color:#fff;font-weight:700;padding:.8rem;border-radius:10px;width:100%; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center">
        <div class="school-logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA Logo">
            @else
                🏫
            @endif
        </div>
        <h1 class="h4 fw-bold mb-1" style="color:var(--primary);">Sta. Lucia Academy</h1>
        <div class="portal-badge"><i class="bi bi-people-fill"></i> Parent / Guardian Portal</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success small py-2">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('parent.login.post') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold small">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="parent@email.com" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold small">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small text-muted" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In as Parent
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('parent.register') }}" class="d-block fw-semibold small text-decoration-none mb-2" style="color:var(--primary-light);">
            Create Parent Account &amp; Connect Child →
        </a>
        <a href="{{ route('student.login') }}" class="small text-muted text-decoration-none">
            ← Back to Student Portal
        </a>
    </div>
</div>
</body>
</html>
