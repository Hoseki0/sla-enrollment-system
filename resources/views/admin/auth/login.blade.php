<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sta. Lucia Academy Enrollment System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#1a3c6e; --accent:#e8a020; --accent-light:#f5c04a; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,#0d1b2e 0%,#1a3c6e 50%,#0d2b50 100%); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative; }
        body::before,body::after { content:''; position:absolute; border-radius:50%; filter:blur(80px); opacity:.15; animation:float 8s ease-in-out infinite; }
        body::before { width:500px;height:500px;background:var(--accent);top:-150px;right:-100px; }
        body::after  { width:400px;height:400px;background:var(--primary);bottom:-100px;left:-100px;animation-delay:-4s; }
        @keyframes float { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-30px) scale(1.05)} }
        .login-wrapper { position:relative;z-index:10;width:100%;max-width:460px;padding:1rem; }
        .login-card { background:rgba(255,255,255,.05);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:2.5rem;box-shadow:0 30px 80px rgba(0,0,0,.5); }
        .school-logo { width:80px;height:80px;background:linear-gradient(135deg,var(--accent),var(--accent-light));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.2rem;margin:0 auto 1rem;box-shadow:0 8px 25px rgba(232,160,32,.4); }
        .school-name { color:#fff;font-size:1.5rem;font-weight:800;text-align:center; }
        .school-sub  { color:var(--accent-light);font-size:.78rem;font-weight:500;text-align:center;letter-spacing:2px;text-transform:uppercase;margin-bottom:2rem; }
        .form-label  { color:rgba(255,255,255,.7);font-size:.82rem;font-weight:500;margin-bottom:.4rem; }
        .form-control { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:10px;padding:.7rem 1rem;font-size:.92rem;transition:all .3s; }
        .form-control:focus { background:rgba(255,255,255,.1);border-color:var(--accent);box-shadow:0 0 0 3px rgba(232,160,32,.2);color:#fff; }
        .form-control::placeholder { color:rgba(255,255,255,.3); }
        .input-group-text { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-right:none;color:rgba(255,255,255,.5);border-radius:10px 0 0 10px; }
        .input-group .form-control { border-left:none;border-radius:0 10px 10px 0; }
        .btn-login { background:linear-gradient(135deg,var(--accent),var(--accent-light));border:none;color:#1a1a1a;font-weight:700;font-size:.95rem;padding:.75rem;border-radius:10px;width:100%;transition:all .3s;box-shadow:0 6px 20px rgba(232,160,32,.35); }
        .btn-login:hover { transform:translateY(-2px);box-shadow:0 10px 30px rgba(232,160,32,.5); }
        .alert-error { background:rgba(220,53,69,.15);border:1px solid rgba(220,53,69,.3);color:#ff6b7a;border-radius:10px;font-size:.85rem;padding:.7rem 1rem; }
        .footer-text { color:rgba(255,255,255,.3);font-size:.75rem;text-align:center;margin-top:1.5rem; }
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
                <img src="{{ asset('images/logo.png') }}" alt="Sta. Lucia Academy" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="Sta. Lucia Academy" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
                🏫
            @endif
        </div>
        <h1 class="school-name">Sta. Lucia Academy</h1>
        <p class="school-sub">Enrollment System</p>

        @if($errors->any())
        <div class="alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="admin@stalucia.edu.ph"
                           value="{{ old('email') }}" required autofocus>
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
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
        <hr>
        <p class="footer-text"><i class="bi bi-shield-lock me-1"></i>Authorized Personnel Only &bull; &copy; {{ date('Y') }} Sta. Lucia Academy</p>
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
