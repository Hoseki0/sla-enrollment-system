<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Sta. Lucia Academy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#1a3c6e; --accent:#e8a020; --accent-light:#f5c04a; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,#0d2b50 0%,#1a3c6e 40%,#15803d 100%); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative; }
        body::before { content:''; position:absolute; width:600px; height:600px; border-radius:50%; background:rgba(232,160,32,.1); top:-200px; right:-200px; filter:blur(60px); animation:float 10s ease-in-out infinite; }
        body::after  { content:''; position:absolute; width:400px; height:400px; border-radius:50%; background:rgba(21,128,61,.15); bottom:-150px; left:-150px; filter:blur(60px); animation:float 10s ease-in-out infinite reverse; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-25px)} }

        .login-wrapper { position:relative;z-index:10;width:100%;max-width:480px;padding:1rem; }
        .login-card { background:rgba(255,255,255,.06);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.13);border-radius:28px;padding:2.5rem;box-shadow:0 32px 80px rgba(0,0,0,.5); }

        .school-logo { width:84px;height:84px;background:linear-gradient(135deg,var(--accent),var(--accent-light));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.3rem;margin:0 auto 1rem;box-shadow:0 8px 28px rgba(232,160,32,.45);overflow:hidden; }
        .school-logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .school-name { color:#fff;font-size:1.5rem;font-weight:800;text-align:center;letter-spacing:-.5px; }
        .portal-badge { display:inline-flex;align-items:center;gap:.4rem;background:rgba(21,128,61,.25);border:1px solid rgba(21,128,61,.4);color:#4ade80;font-size:.72rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:.35rem .9rem;border-radius:20px;margin:.5rem auto 1.75rem;display:flex;justify-content:center;width:fit-content;margin-left:auto;margin-right:auto; }

        .form-label { color:rgba(255,255,255,.7);font-size:.82rem;font-weight:500;margin-bottom:.4rem; }
        .form-control { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:10px;padding:.7rem 1rem;font-size:.92rem;transition:all .3s; }
        .form-control:focus { background:rgba(255,255,255,.1);border-color:var(--accent);box-shadow:0 0 0 3px rgba(232,160,32,.2);color:#fff; }
        .form-control::placeholder { color:rgba(255,255,255,.3); }
        .input-group-text { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-right:none;color:rgba(255,255,255,.5);border-radius:10px 0 0 10px; }
        .input-group .form-control { border-left:none;border-radius:0 10px 10px 0; }

        .btn-login { background:linear-gradient(135deg,#15803d,#16a34a);border:none;color:#fff;font-weight:700;font-size:.95rem;padding:.75rem;border-radius:10px;width:100%;transition:all .3s;box-shadow:0 6px 20px rgba(21,128,61,.4); }
        .btn-login:hover { transform:translateY(-2px);box-shadow:0 10px 30px rgba(21,128,61,.55); }

        .alert-success { background:rgba(21,128,61,.15);border:1px solid rgba(21,128,61,.3);color:#4ade80;border-radius:10px;font-size:.85rem;padding:.7rem 1rem; }
        .action-card { display:flex;align-items:center;gap:.85rem;padding:1rem;border-radius:14px;text-decoration:none;transition:all .3s;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.04); }
        .action-card:hover { background:rgba(255,255,255,.09);transform:translateY(-2px);border-color:rgba(255,255,255,.18); }
        .action-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0; }
        .action-title { color:#fff;font-weight:700;font-size:.9rem;line-height:1.2; }
        .action-sub   { color:rgba(255,255,255,.4);font-size:.73rem;margin-top:2px; }
        .footer-text { color:rgba(255,255,255,.3);font-size:.75rem;text-align:center;margin-top:1.5rem; }
        .admin-link { display:block;text-align:center;color:rgba(255,255,255,.35);font-size:.75rem;margin-top:1rem;text-decoration:none; }
        .admin-link:hover { color:rgba(255,255,255,.6); }
        .pw-wrapper { position:relative; }
        .toggle-pw { background:transparent;border:none;color:rgba(255,255,255,.4);cursor:pointer;position:absolute;right:12px;top:50%;transform:translateY(-50%);z-index:5; }
        hr { border:none;height:1px;background:rgba(255,255,255,.1);margin:1.5rem 0; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="school-logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA Logo">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="SLA Logo">
            @else
                🏫
            @endif
        </div>
        <h1 class="school-name">Sta. Lucia Academy</h1>
        <div class="portal-badge"><i class="bi bi-mortarboard-fill"></i>Student Portal</div>

        @if(session('enroll_success'))
        <div class="alert-success mb-3"><i class="bi bi-check-circle me-2"></i>{{ session('enroll_success') }}</div>
        @endif
        @if(session('register_success'))
        <div class="alert-success mb-3"><i class="bi bi-check-circle me-2"></i>{{ session('register_success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('student.login.post') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Student ID</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="student_id" class="form-control"
                           placeholder="e.g. 2025-0001" value="{{ old('student_id') }}" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="pw-wrapper">
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Enter your password" required style="padding-right:2.5rem;">
                    <button type="button" class="toggle-pw" onclick="togglePw()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);">
                <label class="form-check-label" style="color:rgba(255,255,255,.5);font-size:.82rem;" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Portal
            </button>
        </form>

        <hr>

        {{-- Action buttons --}}
        <div class="d-flex flex-column gap-2">
            <a href="{{ route('student.enroll') }}" class="action-card">
                <div class="action-icon" style="background:linear-gradient(135deg,#e8a020,#f5c04a);">
                    <i class="bi bi-person-plus-fill" style="color:#1a1a1a;"></i>
                </div>
                <div>
                    <div class="action-title">Enroll Now! <span style="font-size:.7rem;background:rgba(232,160,32,.2);color:#f5c04a;padding:.15rem .5rem;border-radius:6px;margin-left:.3rem;">NEW</span></div>
                    <div class="action-sub">First time? Submit your enrollment application here.</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:rgba(255,255,255,.25);"></i>
            </a>
            <a href="{{ route('student.register') }}" class="action-card">
                <div class="action-icon" style="background:linear-gradient(135deg,#2356a0,#1a3c6e);">
                    <i class="bi bi-person-check-fill" style="color:#fff;"></i>
                </div>
                <div>
                    <div class="action-title">Create Account</div>
                    <div class="action-sub">Already enrolled? Set up your portal login here.</div>
                </div>
                <i class="bi bi-chevron-right ms-auto" style="color:rgba(255,255,255,.25);"></i>
            </a>
        </div>

        <p class="footer-text mt-3"><i class="bi bi-shield-lock me-1"></i>Authorized Students Only &bull; &copy; {{ date('Y') }} Sta. Lucia Academy</p>
        <a href="{{ route('admin.login') }}" class="admin-link">
            <i class="bi bi-shield-lock me-1"></i>Admin / Staff Login →
        </a>
    </div>
</div>
<script>
function togglePw() {
    const pw = document.getElementById('password');
    const ic = document.getElementById('eyeIcon');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    ic.className = pw.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
    btn.disabled = true;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
