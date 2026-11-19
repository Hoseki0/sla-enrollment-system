<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Parent Dashboard') | Parent Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#003366; --primary-dark:#001f3f; --primary-light:#00509e;
            --accent:#007bff; --accent-light:#e6f2ff;
            --sidebar-w:260px; --topbar-h:64px;
            --bg:#f8fafc; --card-bg:#fff; --text:#1e293b; --text-muted:#64748b; --border:#e2e8f0;
        }
        * { box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); margin:0; }

        .sidebar { position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:linear-gradient(180deg,var(--primary-dark) 0%,var(--primary) 100%);display:flex;flex-direction:column;z-index:1000;box-shadow:4px 0 20px rgba(0,0,0,.15); }
        .sidebar-brand { padding:1.25rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:.75rem; }
        .brand-icon { width:42px;height:42px;border-radius:12px;background:var(--card-bg);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;overflow:hidden;border:2px solid var(--accent-light); }
        .brand-icon img { width:100%;height:100%;object-fit:cover;border-radius:10px; }
        .brand-name { color:#fff;font-size:.88rem;font-weight:700; }
        .brand-sub  { color:rgba(255,255,255,.45);font-size:.65rem;letter-spacing:1px;text-transform:uppercase; }

        .parent-card { margin:.75rem; background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.85rem;display:flex;align-items:center;gap:.75rem; }
        .parent-avatar { width:42px;height:42px;border-radius:50%;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--primary-dark);font-size:1rem;font-weight:800;flex-shrink:0; }
        .parent-name { color:#fff;font-size:.85rem;font-weight:700;line-height:1.2; }
        .parent-role { color:rgba(255,255,255,.5);font-size:.7rem; }

        .sidebar-nav { flex:1;padding:.5rem .75rem;overflow-y:auto; }
        .nav-section { color:rgba(255,255,255,.35);font-size:.63rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.75rem .75rem .4rem; }
        .nav-link-item { display:flex;align-items:center;gap:.75rem;padding:.65rem .85rem;border-radius:10px;color:rgba(255,255,255,.7);font-size:.87rem;font-weight:500;text-decoration:none;transition:all .2s;margin-bottom:2px; }
        .nav-link-item i { font-size:1.05rem;width:20px;text-align:center; }
        .nav-link-item:hover { background:rgba(255,255,255,.08);color:#fff; }
        .nav-link-item.active { background:var(--accent-light);color:var(--primary-dark);font-weight:700;box-shadow:0 4px 12px rgba(0,0,0,.1); }
        .sidebar-footer { padding:.75rem; }

        .topbar { position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--topbar-h);z-index:900;background:rgba(255,255,255,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1.5rem;gap:1rem; }
        .topbar-title { font-size:1.1rem;font-weight:700;color:var(--text);flex:1; }

        .main-content { margin-left:var(--sidebar-w);margin-top:var(--topbar-h);padding:1.75rem;min-height:calc(100vh - var(--topbar-h)); }
        .card { border:1px solid var(--border);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.04); }
        .card-header { background:transparent;border-bottom:1px solid var(--border);padding:1rem 1.25rem;font-weight:600;font-size:.95rem; }
        .stat-card { border-radius:16px;padding:1.25rem;display:flex;align-items:center;gap:1rem;border:none;box-shadow:0 4px 20px rgba(0,0,0,.07); }
        .stat-icon { width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0; }
        .stat-val   { font-size:1.45rem;font-weight:800;line-height:1; }
        .stat-label { font-size:.76rem;font-weight:500;color:rgba(255,255,255,.8);margin-top:2px; }
        .table th { font-size:.76rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px; }
        .table td { font-size:.88rem;vertical-align:middle; }

        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%);transition:transform .3s; }
            .sidebar.open { transform:translateX(0); }
            .topbar,.main-content { margin-left:0; }
        }
    </style>
    @stack('styles')
</head>
<body>
@php $parentUser = auth('parent')->user(); @endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA Logo">
            @else
                🏫
            @endif
        </div>
        <div>
            <div class="brand-name">Sta. Lucia Academy</div>
            <div class="brand-sub">Parent Portal</div>
        </div>
    </div>

    <div class="parent-card">
        <div class="parent-avatar">{{ $parentUser->initials }}</div>
        <div class="overflow-hidden">
            <div class="parent-name text-truncate">{{ $parentUser->name }}</div>
            <div class="parent-role">{{ $parentUser->relationship }}</div>
        </div>
    </div>

    @if(isset($children) && $children->count() > 0)
    <div class="px-3 mb-2">
        <label class="text-uppercase" style="color:rgba(255,255,255,.45);font-size:.63rem;font-weight:700;letter-spacing:1px;">Viewing Child</label>
        <form method="GET" action="{{ url()->current() }}">
            <select name="student_id" class="form-select form-select-sm mt-1" onchange="this.form.submit()"
                    style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);">
                @foreach($children as $c)
                    <option value="{{ $c->id }}" style="color:#1e293b;" {{ isset($child) && $child->id === $c->id ? 'selected' : '' }}>
                        {{ $c->first_name }} {{ $c->last_name }} ({{ $c->lrn }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    <nav class="sidebar-nav">
        <div class="nav-section">Monitoring</div>
        <a href="{{ route('parent.dashboard') }}" class="nav-link-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Overview
        </a>
        <a href="{{ route('parent.attendance') }}" class="nav-link-item {{ request()->routeIs('parent.attendance') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Attendance Sheet
        </a>
        <a href="{{ route('parent.grades') }}" class="nav-link-item {{ request()->routeIs('parent.grades') ? 'active' : '' }}">
            <i class="bi bi-award"></i> Grades
        </a>
        <a href="{{ route('parent.payments') }}" class="nav-link-item {{ request()->routeIs('parent.payments') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Payments
        </a>
    </nav>

    <div class="sidebar-footer border-top" style="border-color:rgba(255,255,255,.08)!important;">
        <button type="button" class="btn btn-outline-light btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#linkChildModal">
            <i class="bi bi-person-plus me-1"></i> Link Another Child
        </button>
        <form method="POST" action="{{ route('parent.logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
            </button>
        </form>
    </div>
</aside>

<header class="topbar">
    <button class="btn btn-light btn-sm d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list fs-5"></i>
    </button>
    <div class="topbar-title">@yield('title', 'Parent Dashboard')</div>
    @if(isset($child) && $child)
        <span class="badge rounded-pill px-3 py-2" style="background:var(--accent-light);color:var(--primary);font-weight:600;">
            <i class="bi bi-person-badge me-1"></i>{{ $child->first_name }} {{ $child->last_name }}
        </span>
    @endif
</header>

<main class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

{{-- Link Child Modal --}}
<div class="modal fade" id="linkChildModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('parent.link-child') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Connect Child Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Enter your child's LRN and Last Name to link their student account to your Parent Portal.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Child's LRN *</label>
                    <input type="text" name="lrn" class="form-control" placeholder="e.g. 123456789012" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Child's Last Name *</label>
                    <input type="text" name="last_name" class="form-control" placeholder="e.g. Dela Cruz" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Relationship</label>
                    <select name="relationship" class="form-select">
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-semibold">Connect Child</button>
            </div>
        </form>
    </div>
</div>

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
