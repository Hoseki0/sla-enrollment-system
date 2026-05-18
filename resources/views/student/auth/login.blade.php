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
        :root { --primary:#003366; --primary-light:#00509e; --accent:#007bff; --accent-light:#e6f2ff; --text-dark:#1e293b; --text-muted:#64748b; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative; margin:0; }
        body::before { content:''; position:absolute; width:600px; height:600px; border-radius:50%; background:rgba(255,255,255,.05); top:-200px; right:-200px; filter:blur(60px); animation:float 10s ease-in-out infinite; z-index:0; }
        body::after  { content:''; position:absolute; width:400px; height:400px; border-radius:50%; background:rgba(255,255,255,.05); bottom:-150px; left:-150px; filter:blur(60px); animation:float 10s ease-in-out infinite reverse; z-index:0; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-25px)} }
        
        @keyframes popIn { from { opacity: 0; transform: scale(0.95) translateY(15px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes slideToRight { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
        
        .slide-to-right { animation: slideToRight 0.8s cubic-bezier(0.25, 1, 0.5, 1) 0.3s forwards; opacity: 0; }

        .login-wrapper { position:relative;z-index:10;width:100%;max-width:900px;padding:1rem; }
        .login-card { background:#ffffff;border:1px solid rgba(0,0,0,.05);border-radius:28px;box-shadow:0 32px 80px rgba(0,20,50,.3);overflow:hidden; animation: popIn 0.7s cubic-bezier(0.25, 1, 0.5, 1) forwards; }

        .school-logo { width:74px;height:74px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem;box-shadow:0 8px 28px rgba(0,80,158,.2);overflow:hidden;border:2px solid var(--accent-light); }
        .school-logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .school-name { color:var(--primary);font-size:1.4rem;font-weight:800;text-align:center;letter-spacing:-.5px; }
        .portal-badge { display:inline-flex;align-items:center;gap:.4rem;background:var(--accent-light);border:1px solid #cce5ff;color:var(--primary-light);font-size:.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.3rem .8rem;border-radius:20px;margin:.5rem auto 1.5rem;justify-content:center;width:fit-content; }

        .form-label { color:var(--text-dark);font-size:.85rem;font-weight:600;margin-bottom:.4rem; }
        .form-control { background:#f8fafc;border:1px solid #e2e8f0;color:var(--text-dark);border-radius:10px;padding:.7rem 1rem;font-size:.95rem;transition:all .3s; }
        .form-control:focus { background:#fff;border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,123,255,.2);color:var(--text-dark); }
        .form-control::placeholder { color:#94a3b8; }
        .input-group-text { background:#f8fafc;border:1px solid #e2e8f0;border-right:none;color:var(--text-muted);border-radius:10px 0 0 10px; }
        .input-group .form-control { border-left:none;border-radius:0 10px 10px 0; }

        .btn-login { background:linear-gradient(135deg,var(--primary-light),var(--primary));border:none;color:#fff;font-weight:700;font-size:.95rem;padding:.8rem;border-radius:10px;width:100%;transition:all .3s;box-shadow:0 6px 20px rgba(0,80,158,.3); }
        .btn-login:hover { transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,80,158,.5);color:#fff; }

        .alert-success { background:#d1e7dd;border:1px solid #badbcc;color:#0f5132;border-radius:10px;font-size:.85rem;padding:.7rem 1rem; }
        .alert-error { background:#f8d7da;border:1px solid #f5c2c7;color:#842029;border-radius:10px;font-size:.85rem;padding:.7rem 1rem; }
        
        .action-card { display:flex;align-items:center;gap:.85rem;padding:1rem;border-radius:14px;text-decoration:none;transition:all .3s;border:1px solid #e2e8f0;background:#f8fafc; }
        .action-card:hover { background:#fff;transform:translateY(-2px);border-color:var(--accent);box-shadow:0 10px 20px rgba(0,0,0,.03); }
        .action-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0; }
        .action-title { color:var(--text-dark);font-weight:700;font-size:.9rem;line-height:1.2; }
        .action-sub   { color:var(--text-muted);font-size:.75rem;margin-top:2px; }
        .footer-text { color:var(--text-muted);font-size:.75rem;text-align:center; }
        .admin-link { display:block;text-align:center;color:var(--primary-light);font-size:.8rem;font-weight:600;margin-top:1rem;text-decoration:none; }
        .admin-link:hover { color:var(--primary);text-decoration:underline; }
        .pw-wrapper { position:relative; }
        .toggle-pw { background:transparent;border:none;color:#94a3b8;cursor:pointer;position:absolute;right:12px;top:50%;transform:translateY(-50%);z-index:5; }
        .toggle-pw:hover { color:var(--primary); }
        
        .divider-vertical { border-right: 1px solid #e2e8f0; }
        @media (max-width: 767px) {
            .divider-vertical { border-right: none; border-bottom: 1px solid #e2e8f0; }
            body { overflow-y:auto; padding: 2rem 0; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="row g-0">
            <!-- Left Side: Login Form -->
            <div class="col-md-6 p-4 p-md-5 divider-vertical">
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
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" style="border-color:#cbd5e1;">
                        <label class="form-check-label" style="color:var(--text-muted);font-size:.85rem;font-weight:500;" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
            </div>

            <!-- Right Side: Enroll Now Actions -->
            <div class="col-md-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-light slide-to-right" style="overflow: hidden;">
                <div class="text-center mb-4">
                    <h4 style="color:var(--primary);font-weight:800;">Welcome!</h4>
                    <p class="text-muted" style="font-size:0.9rem;">Access your academic records or apply for enrollment.</p>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    <a href="{{ route('student.enroll') }}" class="action-card bg-white">
                        <div class="action-icon" style="background:var(--accent-light);color:var(--primary-light);">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <div class="action-title">Enroll Now! <span style="font-size:.7rem;background:var(--accent);color:#fff;padding:.15rem .5rem;border-radius:6px;margin-left:.3rem;">NEW</span></div>
                            <div class="action-sub">Submit your enrollment application.</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto" style="color:#cbd5e1;"></i>
                    </a>
                    <a href="{{ route('student.register') }}" class="action-card bg-white">
                        <div class="action-icon" style="background:var(--primary);color:#fff;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <div class="action-title">Create Account</div>
                            <div class="action-sub">Set up your portal login here.</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto" style="color:#cbd5e1;"></i>
                    </a>
                </div>

                <div class="mt-auto">
                    <p class="footer-text mb-2"><i class="bi bi-shield-lock me-1"></i>Authorized Students Only &bull; &copy; {{ date('Y') }} SLA</p>
                    <a href="{{ route('admin.login') }}" class="admin-link">
                        Admin / Staff Login →
                    </a>
                </div>
            </div>
        </div>
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
