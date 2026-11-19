<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Dashboard') | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#003366; --primary-dark:#001f3f; --primary-light:#00509e;
            --accent:#007bff; --accent-light:#e6f2ff;
            --navy:#003366; --navy-light:#00509e;
            --sidebar-w:250px; --topbar-h:64px;
            --bg:#f8fafc; --card-bg:#fff; --text:#1e293b; --text-muted:#64748b; --border:#e2e8f0;
        }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); margin:0; }

        /* SIDEBAR */
        .sidebar { position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:linear-gradient(180deg,var(--primary-dark) 0%,var(--primary) 100%);display:flex;flex-direction:column;z-index:1000;box-shadow:4px 0 20px rgba(0,0,0,.15); }
        .sidebar-brand { padding:1.25rem 1.25rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:.75rem; }
        .brand-icon { width:42px;height:42px;border-radius:12px;background:var(--card-bg);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,.1);overflow:hidden;border:2px solid var(--accent-light); }
        .brand-icon img { width:100%;height:100%;object-fit:cover;border-radius:10px; }
        .brand-name { color:#fff;font-size:.88rem;font-weight:700; }
        .brand-sub  { color:rgba(255,255,255,.4);font-size:.65rem;letter-spacing:1px;text-transform:uppercase; }

        .student-card { margin:.75rem; background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.85rem;display:flex;align-items:center;gap:.75rem; }
        .student-avatar { width:44px;height:44px;border-radius:50%;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--primary-dark);font-size:1.05rem;font-weight:800;flex-shrink:0; }
        .student-name  { color:#fff;font-size:.85rem;font-weight:700;line-height:1.2; }
        .student-id    { color:rgba(255,255,255,.45);font-size:.7rem; }

        .sidebar-nav { flex:1;padding:.5rem .75rem;overflow-y:auto; }
        .nav-section { color:rgba(255,255,255,.3);font-size:.63rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.75rem .75rem .4rem; }
        .nav-link-item { display:flex;align-items:center;gap:.75rem;padding:.65rem .85rem;border-radius:10px;color:rgba(255,255,255,.65);font-size:.87rem;font-weight:500;text-decoration:none;transition:all .2s;margin-bottom:2px; }
        .nav-link-item i { font-size:1.05rem;width:20px;text-align:center; }
        .nav-link-item:hover { background:rgba(255,255,255,.08);color:#fff; }
        .nav-link-item.active { background:var(--accent-light);color:var(--primary-dark);font-weight:700;box-shadow:0 4px 12px rgba(0,0,0,.1); }
        .sidebar-footer { padding:.75rem; }

        /* TOPBAR */
        .topbar { position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--topbar-h);z-index:900;background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1.5rem;gap:1rem; }
        .topbar-title { font-size:1.1rem;font-weight:700;color:var(--text);flex:1; }

        /* MAIN */
        .main-content { margin-left:var(--sidebar-w);margin-top:var(--topbar-h);padding:1.75rem;min-height:calc(100vh - var(--topbar-h)); }

        /* CARDS */
        .card { border:1px solid var(--border);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.04); }
        .card-header { background:transparent;border-bottom:1px solid var(--border);padding:1rem 1.25rem;font-weight:600;font-size:.95rem; }
        .stat-card { border-radius:16px;padding:1.4rem;display:flex;align-items:center;gap:1rem;border:none;box-shadow:0 4px 20px rgba(0,0,0,.07);transition:transform .2s,box-shadow .2s; }
        .stat-card:hover { transform:translateY(-3px);box-shadow:0 8px 30px rgba(0,0,0,.12); }
        .stat-icon { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0; }
        .stat-val   { font-size:1.5rem;font-weight:800;line-height:1; }
        .stat-label { font-size:.76rem;font-weight:500;color:rgba(255,255,255,.7);margin-top:2px; }

        /* STATUS badges */
        .badge-enrolled  { background:#dcfce7;color:#15803d; }
        .badge-pending   { background:#fef9c3;color:#a16207; }
        .badge-cancelled { background:#fee2e2;color:#dc2626; }
        .badge-dropped   { background:#f1f5f9;color:#64748b; }
        .badge-jhs { background:#eff6ff;color:#1d4ed8; }
        .badge-shs { background:#f5f3ff;color:#6d28d9; }

        .status-banner { border-radius:16px;padding:1.5rem 2rem;display:flex;align-items:center;gap:1.5rem;border:none; }
        .table th { font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px; }
        .table td { font-size:.88rem;vertical-align:middle; }
        .section-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem; }
        .section-header h2 { font-size:1.3rem;font-weight:700;margin:0; }

        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%);transition:transform .3s; }
            .sidebar.open { transform:translateX(0); }
            .topbar,.main-content { margin-left:0; }
        }
    </style>
    @stack('styles')
</head>
<body>
@php $student = auth('student')->user(); @endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA Logo">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="SLA Logo">
            @else
                🏫
            @endif
        </div>
        <div>
            <div class="brand-name">Sta. Lucia Academy</div>
            <div class="brand-sub">Student Portal</div>
        </div>
    </div>

    <div class="student-card">
        <div class="student-avatar">{{ $student->initials }}</div>
        <div>
            <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
            <div class="student-id">{{ $student->student_id }}</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>
        <a href="{{ route('student.dashboard') }}"
           class="nav-link-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i> My Dashboard
        </a>
        <a href="{{ route('student.profile') }}"
           class="nav-link-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
        <a href="{{ route('student.payments.index') }}"
           class="nav-link-item {{ request()->routeIs('student.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Payments
        </a>
        <a href="{{ route('student.grades.index') }}"
           class="nav-link-item {{ request()->routeIs('student.grades.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i> My Grades
        </a>
        <a href="{{ route('student.exams.index') }}"
           class="nav-link-item {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Examinations
        </a>

        <div class="nav-section" style="margin-top:.75rem;">Services</div>
        <a href="{{ route('student.re-enroll') }}"
           class="nav-link-item {{ request()->routeIs('student.re-enroll*') ? 'active' : '' }}">
            <i class="bi bi-journal-plus"></i> Online Enrollment
        </a>
        <a href="{{ route('student.document-requests.index') }}"
           class="nav-link-item {{ request()->routeIs('student.document-requests.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Document Requests
        </a>
        <a href="{{ route('student.certificate') }}"
           class="nav-link-item {{ request()->routeIs('student.certificate') ? 'active' : '' }}">
            <i class="bi bi-award"></i> My Certificate
        </a>
        <a href="{{ route('student.profile.edit') }}"
           class="nav-link-item {{ request()->routeIs('student.profile.edit') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> Edit Profile
        </a>

        <div class="nav-section" style="margin-top:.5rem;">Account</div>
        <form method="POST" action="{{ route('student.logout') }}">
            @csrf
            <button type="submit" class="nav-link-item w-100 border-0 bg-transparent text-start">
                <i class="bi bi-box-arrow-right"></i> Sign Out
            </button>
        </form>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('admin.login') }}" class="nav-link-item" style="font-size:.78rem;opacity:.5;">
            <i class="bi bi-shield-lock"></i> Admin Portal
        </a>
    </div>
</aside>

<header class="topbar">
    <button class="btn btn-sm d-md-none border-0 fs-5" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title">@yield('title', 'My Dashboard')</div>
    <form method="POST" action="{{ route('student.logout') }}">
        @csrf
        <button type="submit" class="btn btn-sm border-0" style="color:var(--text-muted);">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </form>
</header>

<main class="main-content">
    @if(session('success'))
    <div class="alert border-0 rounded-3 mb-3 d-flex align-items-center" style="background:#d1e7dd;color:#0f5132;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert border-0 rounded-3 mb-3 d-flex align-items-center" style="background:#fee2e2;color:#dc2626;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Announcements Banner --}}
    @php
        $student = auth('student')->user();
        $levelType = $student?->latestEnrollment?->level_type ?? 'JHS';
        $announcements = \App\Models\Announcement::published()
            ->forAudience($levelType)
            ->orderByPriority()
            ->latest()
            ->take(3)
            ->get();
    @endphp
    @foreach($announcements as $ann)
    <div class="alert border-0 rounded-3 mb-2 d-flex align-items-start gap-2"
         style="background:{{ $ann->priority === 'urgent' ? '#fee2e2' : ($ann->priority === 'important' ? '#fffbeb' : '#eff6ff') }};
                color:{{ $ann->priority === 'urgent' ? '#991b1b' : ($ann->priority === 'important' ? '#92400e' : '#1e40af') }};">
        <i class="bi {{ $ann->priority_icon }} mt-1 flex-shrink-0"></i>
        <div>
            <strong>{{ $ann->title }}</strong>
            <div style="font-size:.84rem;margin-top:2px;">{{ $ann->body }}</div>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endforeach

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
@stack('scripts')
</body>
</html>
