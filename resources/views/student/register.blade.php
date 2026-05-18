<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Sta. Lucia Academy Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#1a3c6e; --accent:#e8a020; --green:#15803d; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,#0d2b50 0%,#1a3c6e 50%,#15803d 100%); display:flex; align-items:center; justify-content:center; padding:1.5rem; }
        .card-wrap { width:100%;max-width:480px; }
        .glass-card { background:rgba(255,255,255,.06);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:2.5rem;box-shadow:0 30px 70px rgba(0,0,0,.5); }
        .logo { width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#f5c04a);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 1rem;box-shadow:0 8px 24px rgba(232,160,32,.4);overflow:hidden; }
        .logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        h1 { color:#fff;font-size:1.45rem;font-weight:800;text-align:center; }
        .sub { color:rgba(255,255,255,.45);font-size:.8rem;text-align:center;margin-bottom:2rem; }
        .form-label { color:rgba(255,255,255,.65);font-size:.82rem;font-weight:500;margin-bottom:.35rem; }
        .form-control { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:10px;padding:.65rem 1rem;font-size:.9rem; }
        .form-control:focus { background:rgba(255,255,255,.1);border-color:var(--accent);box-shadow:0 0 0 3px rgba(232,160,32,.15);color:#fff; }
        .form-control::placeholder { color:rgba(255,255,255,.25); }
        .btn-create { background:linear-gradient(135deg,var(--primary),#2356a0);border:none;color:#fff;font-weight:700;font-size:.95rem;padding:.75rem;border-radius:10px;width:100%;box-shadow:0 6px 20px rgba(26,60,110,.5);transition:all .3s; }
        .btn-create:hover { transform:translateY(-2px);box-shadow:0 10px 28px rgba(26,60,110,.65); }
        .info-box { background:rgba(232,160,32,.08);border:1px solid rgba(232,160,32,.2);border-radius:12px;padding:.9rem 1rem;color:rgba(255,255,255,.6);font-size:.8rem;margin-bottom:1.25rem;line-height:1.6; }
        .alert-error { background:rgba(220,53,69,.15);border:1px solid rgba(220,53,69,.3);color:#ff6b7a;border-radius:10px;font-size:.85rem;padding:.7rem 1rem;margin-bottom:1rem; }
        .divider { border:none;height:1px;background:rgba(255,255,255,.1);margin:1.25rem 0; }
        .back-link { display:block;text-align:center;color:rgba(255,255,255,.35);font-size:.8rem;text-decoration:none;margin-top:1rem; }
        .back-link:hover { color:rgba(255,255,255,.6); }
        .pw-wrapper { position:relative; }
        .toggle-pw { background:transparent;border:none;color:rgba(255,255,255,.35);cursor:pointer;position:absolute;right:10px;top:50%;transform:translateY(-50%); }
    </style>
</head>
<body>
<div class="card-wrap">
    <div class="glass-card">
        <div class="logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="SLA">
            @else 🏫 @endif
        </div>
        <h1>Create Your Account</h1>
        <p class="sub">Already enrolled at Sta. Lucia Academy? Set up your Student Portal account.</p>

        <div class="info-box">
            <i class="bi bi-info-circle me-2" style="color:#f5c04a;"></i>
            You need your <strong style="color:#f5c04a;">Student ID</strong> (e.g. <code style="color:#f5c04a;">2025-0001</code>) and your <strong style="color:#f5c04a;">Last Name</strong> to verify your identity.
        </div>

        @if($errors->any())
        <div class="alert-error"><i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('student.register.post') }}" id="registerForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Student ID <span style="color:#f87171;">*</span></label>
                <input type="text" name="student_id" class="form-control" placeholder="e.g. 2025-0001"
                       value="{{ old('student_id') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name <span style="color:#f87171;">*</span></label>
                <input type="text" name="last_name" class="form-control" placeholder="Your last name (for verification)"
                       value="{{ old('last_name') }}" required>
                <small style="color:rgba(255,255,255,.3);font-size:.73rem;">Used to verify your identity — must match our records.</small>
            </div>
            <hr class="divider">
            <div class="mb-3">
                <label class="form-label">New Password <span style="color:#f87171;">*</span></label>
                <div class="pw-wrapper">
                    <input type="password" name="password" id="pw1" class="form-control"
                           placeholder="At least 6 characters" required style="padding-right:2.5rem;">
                    <button type="button" class="toggle-pw" onclick="togglePw('pw1','eye1')"><i class="bi bi-eye" id="eye1"></i></button>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password <span style="color:#f87171;">*</span></label>
                <div class="pw-wrapper">
                    <input type="password" name="password_confirmation" id="pw2" class="form-control"
                           placeholder="Repeat your password" required style="padding-right:2.5rem;">
                    <button type="button" class="toggle-pw" onclick="togglePw('pw2','eye2')"><i class="bi bi-eye" id="eye2"></i></button>
                </div>
            </div>

            <button type="submit" class="btn-create" id="submitBtn">
                <i class="bi bi-person-check me-2"></i>Create Account
            </button>
        </form>

        <a href="{{ route('student.login') }}" class="back-link">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
        </a>
    </div>
</div>

<script>
function togglePw(id, iconId) {
    const pw = document.getElementById(id);
    const ic = document.getElementById(iconId);
    pw.type = pw.type === 'password' ? 'text' : 'password';
    ic.className = pw.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
document.getElementById('registerForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
    btn.disabled = true;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
