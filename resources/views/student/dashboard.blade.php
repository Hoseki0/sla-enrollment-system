@extends('layouts.student')
@section('title', 'My Dashboard')

@section('content')
@php
    $statusColors = [
        'Enrolled'  => ['bg'=>'linear-gradient(135deg,#15803d,#16a34a)', 'icon'=>'bi-patch-check-fill', 'text'=>'#fff'],
        'Pending'   => ['bg'=>'linear-gradient(135deg,#a16207,#ca8a04)', 'icon'=>'bi-hourglass-split',  'text'=>'#fff'],
        'Cancelled' => ['bg'=>'linear-gradient(135deg,#dc2626,#ef4444)', 'icon'=>'bi-x-circle-fill',   'text'=>'#fff'],
        'Dropped'   => ['bg'=>'linear-gradient(135deg,#475569,#64748b)', 'icon'=>'bi-dash-circle-fill', 'text'=>'#fff'],
        'none'      => ['bg'=>'linear-gradient(135deg,#94a3b8,#64748b)', 'icon'=>'bi-clipboard-x',     'text'=>'#fff'],
    ];
    $status     = $latestEnrollment?->status ?? 'none';
    $sc         = $statusColors[$status] ?? $statusColors['none'];
@endphp

{{-- Welcome Banner --}}
<div class="status-banner mb-4" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
    <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;">
        <i class="bi {{ $sc['icon'] }}"></i>
    </div>
    <div class="flex-grow-1">
        <div style="font-size:.78rem;opacity:.7;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Enrollment Status — SY {{ $sy?->year_label ?? 'N/A' }}</div>
        <div style="font-size:1.6rem;font-weight:800;line-height:1.1;margin:.25rem 0;">
            @if($latestEnrollment)
                {{ $latestEnrollment->status }}
            @else
                Not Yet Enrolled
            @endif
        </div>
        @if($latestEnrollment)
        <div style="opacity:.75;font-size:.85rem;">
            {{ $latestEnrollment->grade_level }}
            @if($latestEnrollment->strand) · {{ $latestEnrollment->strand }} @endif
            @if($latestEnrollment->section) · {{ $latestEnrollment->section->section_name }} @endif
        </div>
        @else
        <div style="opacity:.85;font-size:.85rem;margin-top:2px;">No enrollment record found for SY {{ $sy?->year_label }}.</div>
        @endif
    </div>
    @if($latestEnrollment)
    <div class="text-end">
        <div style="opacity:.6;font-size:.72rem;text-transform:uppercase;letter-spacing:1px;">Enrollment No.</div>
        <div style="font-weight:700;font-size:.95rem;">{{ $latestEnrollment->enrollment_no }}</div>
        <div style="opacity:.6;font-size:.78rem;margin-top:.25rem;">{{ $latestEnrollment->enrollment_date?->format('M d, Y') }}</div>
    </div>
    @else
    <div>
        <a href="{{ route('student.re-enroll') }}" class="btn btn-light fw-bold px-3 py-2 rounded-pill" style="color:var(--primary-dark);white-space:nowrap;">
            <i class="bi bi-journal-plus me-1"></i> Enroll Now
        </a>
    </div>
    @endif
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="stat-card" style="background:linear-gradient(135deg,#a16207,#ca8a04);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-hourglass text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ $enrollmentHistory->where('status','Pending')->count() }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-mortarboard text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ $latestEnrollment?->grade_level ? explode(' ',$latestEnrollment->grade_level)[1] : '—' }}</div>
                <div class="stat-label">Current Grade</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Current Enrollment Details --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-journal-text" style="color:#1a3c6e;"></i>
                <span>Current Enrollment</span>
            </div>
            <div class="card-body">
                @if($latestEnrollment)
                <table class="table table-sm mb-0" style="font-size:.88rem;">
                    <tr>
                        <td class="text-muted fw-semibold" style="width:45%;font-size:.78rem;">ENROLLMENT NO.</td>
                        <td class="fw-bold" style="color:#1a3c6e;font-size:.8rem;">{{ $latestEnrollment->enrollment_no }}</td>
                    </tr>
                    <tr><td class="text-muted fw-semibold" style="font-size:.78rem;">SCHOOL YEAR</td><td>SY {{ $latestEnrollment->schoolYear->year_label }}</td></tr>
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.78rem;">LEVEL</td>
                        <td><span class="badge rounded-pill px-2 {{ $latestEnrollment->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $latestEnrollment->level_type }}</span></td>
                    </tr>
                    <tr><td class="text-muted fw-semibold" style="font-size:.78rem;">GRADE</td><td>{{ $latestEnrollment->grade_level }}</td></tr>
                    @if($latestEnrollment->track)<tr><td class="text-muted fw-semibold" style="font-size:.78rem;">TRACK</td><td>{{ $latestEnrollment->track }}</td></tr>@endif
                    @if($latestEnrollment->strand)<tr><td class="text-muted fw-semibold" style="font-size:.78rem;">STRAND</td><td>{{ $latestEnrollment->strand }}</td></tr>@endif
                    @if($latestEnrollment->semester)<tr><td class="text-muted fw-semibold" style="font-size:.78rem;">SEMESTER</td><td>{{ $latestEnrollment->semester }}</td></tr>@endif
                    <tr><td class="text-muted fw-semibold" style="font-size:.78rem;">SECTION</td><td>{{ $latestEnrollment->section?->section_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-semibold" style="font-size:.78rem;">TYPE</td><td>{{ $latestEnrollment->enrollment_type }}</td></tr>
                    <tr><td class="text-muted fw-semibold" style="font-size:.78rem;">DATE</td><td>{{ $latestEnrollment->enrollment_date?->format('M d, Y') }}</td></tr>
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.78rem;">STATUS</td>
                        <td><span class="badge rounded-pill px-2 badge-{{ strtolower($latestEnrollment->status) }}">{{ $latestEnrollment->status }}</span></td>
                    </tr>
                </table>
                @else
                <div class="text-center py-4" style="color:#94a3b8;">
                    <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-25"></i>
                    No enrollment record for the current school year.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Enrollment History --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history" style="color:#1a3c6e;"></i>
                <span>Enrollment History</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8faff;">
                            <tr>
                                <th class="ps-3">School Year</th>
                                <th>Level</th>
                                <th>Grade</th>
                                <th>Strand</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($enrollmentHistory as $e)
                        <tr>
                            <td class="ps-3" style="font-size:.82rem;font-weight:600;">SY {{ $e->schoolYear->year_label }}</td>
                            <td><span class="badge rounded-pill px-2 {{ $e->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $e->level_type }}</span></td>
                            <td style="font-size:.85rem;">{{ $e->grade_level }}</td>
                            <td style="font-size:.82rem;">{{ $e->strand ?? '—' }}</td>
                            <td style="font-size:.82rem;">{{ $e->section?->section_name ?? '—' }}</td>
                            <td><span class="badge rounded-pill px-2 badge-{{ strtolower($e->status) }}">{{ $e->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No enrollment history found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
