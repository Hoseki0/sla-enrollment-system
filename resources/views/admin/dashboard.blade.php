@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="section-header">
    <div>
        <h2>Dashboard</h2>
        <p>Welcome back, {{ auth('admin')->user()->name }}! Here's the overview.</p>
    </div>
    <a href="{{ route('admin.enrollments.create') }}" class="btn text-white fw-semibold px-3"
       style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;">
        <i class="bi bi-person-plus me-1"></i>New Enrollment
    </a>
</div>

{{-- Today at a Glance --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:none;">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center flex-wrap gap-4">
                    <div style="color:rgba(255,255,255,.5);font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;flex-shrink:0;">
                        <i class="bi bi-sun me-1"></i> Today at a Glance
                    </div>
                    <div class="d-flex gap-4 flex-wrap">
                        <div>
                            <div style="color:#fff;font-size:1.5rem;font-weight:800;line-height:1;">{{ $todayStats['enrollments_today'] }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.72rem;">Enrollments Today</div>
                        </div>
                        <div>
                            <div style="color:#4ade80;font-size:1.5rem;font-weight:800;line-height:1;">{{ $todayStats['payments_today'] }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.72rem;">Payments Today</div>
                        </div>
                        <div>
                            <div style="color:#fbbf24;font-size:1.5rem;font-weight:800;line-height:1;">₱{{ number_format($todayStats['collected_today'], 0) }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.72rem;">Collected Today</div>
                        </div>
                        <div>
                            <div style="color:#60a5fa;font-size:1.5rem;font-weight:800;line-height:1;">{{ $pendingDocRequests }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.72rem;">Doc Requests Pending</div>
                        </div>
                        <div>
                            <div style="color:#a78bfa;font-size:1.5rem;font-weight:800;line-height:1;">{{ $activeAnnouncements }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.72rem;">Active Announcements</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Students', 'val'=>$stats['total_students'],  'icon'=>'bi-people-fill',         'bg'=>'#1a3c6e,#2356a0'],
        ['label'=>'Enrolled',       'val'=>$stats['total_enrolled'],  'icon'=>'bi-patch-check-fill',    'bg'=>'#15803d,#16a34a'],
        ['label'=>'Pending',        'val'=>$stats['total_pending'],   'icon'=>'bi-hourglass-split',     'bg'=>'#a16207,#ca8a04'],
        ['label'=>'JHS Enrolled',   'val'=>$stats['total_jhs'],       'icon'=>'bi-book-fill',           'bg'=>'#1d4ed8,#2563eb'],
        ['label'=>'SHS Enrolled',   'val'=>$stats['total_shs'],       'icon'=>'bi-mortarboard-fill',    'bg'=>'#6d28d9,#7c3aed'],
        ['label'=>'New Enrollees',  'val'=>$stats['total_new'],       'icon'=>'bi-person-fill-add',     'bg'=>'#0369a1,#0ea5e9'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card" style="background:linear-gradient(135deg,{{ $c['bg'] }});">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);">
                <i class="bi {{ $c['icon'] }} text-white"></i>
            </div>
            <div>
                <div class="stat-val text-white">{{ number_format($c['val']) }}</div>
                <div class="stat-label" style="color:rgba(255,255,255,.7);">{{ $c['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>Enrollment by Grade Level</span>
                <span class="badge rounded-pill" style="background:#eff6ff;color:#1d4ed8;font-size:.72rem;">
                    SY {{ $sy?->year_label ?? 'N/A' }}
                </span>
            </div>
            <div class="card-body"><canvas id="gradeChart" height="150"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-2" style="color:#7c3aed;"></i>SHS Strand Distribution</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="strandChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-graph-up me-2" style="color:#15803d;"></i>Monthly Trend</div>
            <div class="card-body"><canvas id="trendChart" height="180"></canvas></div>
        </div>
    </div>
</div>

{{-- Quick Actions + Recent Enrollments --}}
<div class="row g-3">
    {{-- Quick Actions --}}
    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions</div>
            <div class="card-body d-flex flex-column gap-2">
                @php $actions = [
                    ['label'=>'New Enrollment',    'route'=>'admin.enrollments.create',     'icon'=>'bi-person-plus',       'color'=>'#1a3c6e'],
                    ['label'=>'Manage Payments',   'route'=>'admin.payments.index',          'icon'=>'bi-credit-card',       'color'=>'#15803d'],
                    ['label'=>'Enter Grades',      'route'=>'admin.grades.index',            'icon'=>'bi-mortarboard',       'color'=>'#6d28d9'],
                    ['label'=>'Enrollment Report', 'route'=>'admin.reports.enrollments',     'icon'=>'bi-file-earmark-text', 'color'=>'#0369a1'],
                    ['label'=>'Doc Requests',      'route'=>'admin.document-requests.index', 'icon'=>'bi-file-earmark',      'color'=>'#a16207'],
                    ['label'=>'Post Announcement', 'route'=>'admin.announcements.create',    'icon'=>'bi-megaphone',         'color'=>'#dc2626'],
                ]; @endphp
                @foreach($actions as $act)
                <a href="{{ route($act['route']) }}"
                   class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none"
                   style="background:#f8faff;border:1px solid #e2e8f0;transition:.2s;font-size:.85rem;font-weight:600;color:{{ $act['color'] }};"
                   onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#f8faff'">
                    <i class="bi {{ $act['icon'] }}"></i> {{ $act['label'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="col-lg-9">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2"></i>Recent Enrollments</span>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm" style="font-size:.78rem;color:#1a3c6e;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8faff;">
                            <tr>
                                <th class="ps-3">Enrollment No.</th><th>Student</th><th>Level</th>
                                <th>Grade</th><th>Type</th><th>Status</th><th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentEnrollments as $e)
                        <tr>
                            <td class="ps-3 fw-semibold text-primary" style="font-size:.82rem;">
                                <a href="{{ route('admin.enrollments.show', $e) }}" class="text-decoration-none">{{ $e->enrollment_no }}</a>
                            </td>
                            <td class="fw-semibold">{{ $e->student->full_name }}</td>
                            <td><span class="badge rounded-pill px-2 {{ $e->level_type === 'JHS' ? 'badge-jhs' : 'badge-shs' }}">{{ $e->level_type }}</span></td>
                            <td style="font-size:.85rem;">{{ $e->grade_level }}</td>
                            <td style="font-size:.82rem;">{{ $e->enrollment_type }}</td>
                            <td><span class="badge rounded-pill px-2 {{ $e->status_badge_class }}">{{ $e->status }}</span></td>
                            <td style="font-size:.82rem;color:#64748b;">{{ $e->enrollment_date->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No enrollments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const gradeLabels = @json($gradeBreakdown->pluck('grade_level'));
const gradeCounts = @json($gradeBreakdown->pluck('cnt'));
new Chart(document.getElementById('gradeChart'), {
    type: 'bar',
    data: { labels: gradeLabels, datasets: [{ label: 'Enrolled', data: gradeCounts,
        backgroundColor: ['#1a3c6e','#2356a0','#1d4ed8','#3b82f6','#6d28d9','#7c3aed','#8b5cf6','#a78bfa','#15803d','#16a34a'],
        borderRadius: 8, borderSkipped: false }] },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});

const strandLabels = @json($strandBreakdown->pluck('strand'));
const strandCounts = @json($strandBreakdown->pluck('cnt'));
new Chart(document.getElementById('strandChart'), {
    type: 'doughnut',
    data: { labels: strandLabels, datasets: [{ data: strandCounts,
        backgroundColor: ['#1a3c6e','#e8a020','#6d28d9','#15803d','#0369a1'], borderWidth: 2, borderColor: '#fff' }] },
    options: { responsive: true, cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } } }
});

const trendData = @json($monthlyTrend);
const trendLabels = trendData.map(d => {
    const [y, m] = d.month.split('-');
    return new Date(y, m-1).toLocaleString('default', { month: 'short' });
});
const trendCounts = trendData.map(d => d.cnt);
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: { labels: trendLabels, datasets: [{ label: 'Enrollments', data: trendCounts,
        borderColor: '#2356a0', backgroundColor: 'rgba(35,86,160,.1)',
        borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#2356a0', fill: true, tension: .4 }] },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                  x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
});
</script>
@endpush
