<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Sta. Lucia Academy</title>
    <meta name="description" content="Sta. Lucia Academy Enrollment System">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #003366; --primary-dark: #001f3f; --primary-light: #00509e;
            --accent: #007bff; --accent-light: #e6f2ff;
            --sidebar-w: 260px; --topbar-h: 64px;
            --bg: #f8fafc; --card-bg: #fff; --text: #1e293b; --text-muted: #64748b; --border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; }

        /* SIDEBAR */
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            display: flex; flex-direction: column; z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,.15);
        }
        .sidebar-brand { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; gap: .75rem; }
        .brand-icon { width: 42px; height: 42px; border-radius: 12px; background: var(--card-bg); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,.1); border: 2px solid var(--accent-light); }
        .brand-name { color: #fff; font-size: .9rem; font-weight: 700; }
        .brand-sub  { color: rgba(255,255,255,.45); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
        .sidebar-sy { margin: .75rem 1rem; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 8px; padding: .5rem .75rem; color: var(--accent-light); font-size: .75rem; font-weight: 600; display: flex; align-items: center; gap: .4rem; }
        .sidebar-nav { flex: 1; padding: .5rem .75rem; overflow-y: auto; }
        .nav-section { color: rgba(255,255,255,.3); font-size: .65rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: .75rem .75rem .4rem; }
        .nav-link-item { display: flex; align-items: center; gap: .75rem; padding: .65rem .85rem; border-radius: 10px; color: rgba(255,255,255,.65); font-size: .88rem; font-weight: 500; text-decoration: none; transition: all .2s; margin-bottom: 2px; }
        .nav-link-item i { font-size: 1.05rem; width: 20px; text-align: center; }
        .nav-link-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .nav-link-item.active { background: var(--accent-light); color: var(--primary-dark); font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        .sidebar-footer { padding: 1rem .75rem; border-top: 1px solid rgba(255,255,255,.08); }
        .admin-info { display: flex; align-items: center; gap: .75rem; padding: .6rem .75rem; border-radius: 10px; background: rgba(255,255,255,.05); }
        .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--accent-light); display: flex; align-items: center; justify-content: center; color: var(--primary-dark); font-size: .85rem; font-weight: 700; flex-shrink: 0; }
        .admin-name { color: #fff; font-size: .8rem; font-weight: 600; }
        .admin-role { color: rgba(255,255,255,.4); font-size: .68rem; text-transform: capitalize; }

        /* TOPBAR */
        .topbar { position: fixed; top: 0; left: var(--sidebar-w); right: 0; height: var(--topbar-h); z-index: 900; background: rgba(240,244,248,.9); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem; }
        .topbar-title { font-size: 1.15rem; font-weight: 700; color: var(--text); flex: 1; }

        /* MAIN */
        .main-content { margin-left: var(--sidebar-w); margin-top: var(--topbar-h); padding: 1.75rem; min-height: calc(100vh - var(--topbar-h)); }

        /* CARDS */
        .card { border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.04); }
        .card-header { background: transparent; border-bottom: 1px solid var(--border); padding: 1rem 1.25rem; font-weight: 600; font-size: .95rem; }
        .stat-card { border-radius: 16px; padding: 1.4rem; display: flex; align-items: center; gap: 1rem; border: none; box-shadow: 0 4px 20px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.12); }
        .stat-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .stat-val   { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: .78rem; font-weight: 500; color: var(--text-muted); margin-top: 2px; }

        /* BADGES */
        .badge-enrolled  { background: #dcfce7; color: #15803d; }
        .badge-pending   { background: #fef9c3; color: #a16207; }
        .badge-cancelled { background: #fee2e2; color: #dc2626; }
        .badge-dropped   { background: #f1f5f9; color: #64748b; }
        .badge-jhs { background: #eff6ff; color: #1d4ed8; }
        .badge-shs { background: #f5f3ff; color: #6d28d9; }

        /* TABLE */
        .table th { font-size: .78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }
        .table td { font-size: .88rem; vertical-align: middle; }
        .table-hover tbody tr:hover { background: #f8faff; }

        /* FORM */
        .form-label { font-size: .82rem; font-weight: 600; color: var(--text-muted); }
        .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; font-size: .9rem; padding: .55rem .85rem; transition: all .2s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(35,86,160,.1); }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .section-header h2 { font-size: 1.3rem; font-weight: 700; margin: 0; }
        .section-header p  { color: var(--text-muted); font-size: .85rem; margin: 2px 0 0; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.open { transform: translateX(0); }
            .topbar, .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
@php $admin = auth('admin')->user(); $sy = \App\Models\SchoolYear::active(); @endphp
@php
$navItems = [
    // Core
    ['icon'=>'bi-speedometer2',   'label'=>'Dashboard',         'route'=>'admin.dashboard'],
    ['icon'=>'bi-person-plus',    'label'=>'New Enrollment',    'route'=>'admin.enrollments.create'],
    ['icon'=>'bi-journal-check',  'label'=>'Enrollments',       'route'=>'admin.enrollments.index'],
    ['icon'=>'bi-people',         'label'=>'Students',          'route'=>'admin.students.index'],
    ['icon'=>'bi-credit-card',    'label'=>'Payments',          'route'=>'admin.payments.index'],
    ['icon'=>'bi-cash-stack',     'label'=>'Fee Schedules',     'route'=>'admin.fees.index'],
    ['icon'=>'bi-mortarboard',    'label'=>'Grades',            'route'=>'admin.grades.index'],
    ['icon'=>'bi-calendar-check', 'label'=>'Attendance',        'route'=>'admin.attendance.index'],
    ['icon'=>'bi-grid-3x3-gap',   'label'=>'Sections',          'route'=>'admin.sections.index'],
    ['icon'=>'bi-calendar3',      'label'=>'School Years',      'route'=>'admin.school-years.index'],
];
$reportItems = [
    ['icon'=>'bi-file-earmark-bar-graph', 'label'=>'Enrollment Report',  'route'=>'admin.reports.enrollments'],
    ['icon'=>'bi-file-earmark-spreadsheet','label'=>'Payment Report',    'route'=>'admin.reports.payments'],
];
$commsItems = [
    ['icon'=>'bi-megaphone',     'label'=>'Announcements',   'route'=>'admin.announcements.index'],
    ['icon'=>'bi-file-earmark',  'label'=>'Doc Requests',    'route'=>'admin.document-requests.index'],
];
$systemItems = [
    ['icon'=>'bi-person-badge',  'label'=>'Admin Users',    'route'=>'admin.users.index'],
    ['icon'=>'bi-activity',      'label'=>'Activity Log',   'route'=>'admin.activity-log.index'],
    ['icon'=>'bi-gear',          'label'=>'Settings',       'route'=>'admin.settings.index'],
];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="overflow:hidden;padding:0;">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA Logo" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="SLA Logo" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
            @else
                🏫
            @endif
        </div>
        <div>
            <div class="brand-name">Sta. Lucia Academy</div>
            <div class="brand-sub">Enrollment System</div>
        </div>
    </div>
    @if($sy)
    <div class="sidebar-sy"><i class="bi bi-calendar-check"></i> SY {{ $sy->year_label }}</div>
    @endif
    <nav class="sidebar-nav">
        <div class="nav-section">Main Menu</div>
        @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link-item {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'*') ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
        </a>
        @endforeach

        <div class="nav-section" style="margin-top:.75rem;">Reports</div>
        @foreach($reportItems as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link-item {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
        </a>
        @endforeach

        <div class="nav-section" style="margin-top:.75rem;">Communications</div>
        @foreach($commsItems as $item)
        @php
            $badge = null;
            if ($item['route'] === 'admin.document-requests.index') {
                $badge = \App\Models\DocumentRequest::where('status','pending')->count();
            }
        @endphp
        <a href="{{ route($item['route']) }}"
           class="nav-link-item {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
            @if($badge)
                <span class="ms-auto badge rounded-pill" style="background:#ef4444;color:#fff;font-size:.65rem;">{{ $badge }}</span>
            @endif
        </a>
        @endforeach

        <div class="nav-section" style="margin-top:.75rem;">System</div>
        @foreach($systemItems as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link-item {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
        </a>
        @endforeach

        <div class="nav-section" style="margin-top:.5rem;">Account</div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link-item w-100 border-0 bg-transparent text-start">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ $admin->name }}</div>
                <div class="admin-role">{{ $admin->role_label }}</div>
            </div>
        </div>
    </div>
</aside>

<header class="topbar">
    <button class="btn btn-sm d-md-none border-0 fs-5" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title">@yield('title', 'Dashboard')</div>
    <div class="d-flex align-items-center gap-2">
        @if($sy)
        <span class="badge rounded-pill" style="background:#eff6ff;color:#1d4ed8;font-size:.75rem;padding:.4rem .8rem;">
            <i class="bi bi-calendar3 me-1"></i>SY {{ $sy->year_label }}
        </span>
        @endif
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm border-0" style="color:var(--text-muted);">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>

<main class="main-content">
    @if(session('success'))
    <div class="alert border-0 rounded-3 mb-3 d-flex align-items-center" style="background:#d1e7dd;color:#0f5132;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert border-0 rounded-3 mb-3" style="background:#fee2e2;color:#dc2626;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.datatable').forEach(function(el) {
        $(el).DataTable({ responsive: true, pageLength: 25, language: { search: '', searchPlaceholder: 'Search...' } });
    });
});
</script>
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
