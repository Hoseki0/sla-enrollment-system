@extends('layouts.parent')
@section('title', 'Child Overview')

@section('content')
@if(!$child)
    <div class="card p-5 text-center">
        <i class="bi bi-person-plus fs-1 text-muted mb-3"></i>
        <h5 class="fw-bold">No Child Connected Yet</h5>
        <p class="text-muted">Connect your child's student account using their LRN and Last Name to monitor their attendance, grades, and payments.</p>
        <div>
            <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#linkChildModal">
                <i class="bi bi-link-45deg me-1"></i>Connect Child Account
            </button>
        </div>
    </div>
@else
    {{-- Today's School Attendance Banner --}}
    @php
        $todayStatus = $todayAttendance?->status;
        $bannerBg = match($todayStatus) {
            'Present' => 'linear-gradient(135deg,#15803d,#16a34a)',
            'Late'    => 'linear-gradient(135deg,#a16207,#ca8a04)',
            'Absent'  => 'linear-gradient(135deg,#dc2626,#ef4444)',
            'Excused' => 'linear-gradient(135deg,#0284c7,#0ea5e9)',
            default   => 'linear-gradient(135deg,#1a3c6e,#2356a0)',
        };
    @endphp
    <div class="p-4 rounded-4 text-white mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background:{{ $bannerBg }};">
        <div>
            <div class="text-uppercase small fw-semibold opacity-75">Today's School Attendance — {{ now()->format('M d, Y') }}</div>
            <div class="h3 fw-bold mb-1">
                @if($todayAttendance)
                    {{ $child->first_name }} is {{ $todayAttendance->status }}
                    @if($todayAttendance->time_in) <span class="fs-6 fw-normal opacity-75">(Time in: {{ $todayAttendance->time_in }})</span> @endif
                @else
                    No attendance recorded yet for today
                @endif
            </div>
            <div class="small opacity-75">
                {{ $child->full_name }} &bull; LRN: {{ $child->lrn }}
                @if($latestEnrollment)
                    &bull; {{ $latestEnrollment->grade_level }}
                    @if($latestEnrollment->strand) ({{ $latestEnrollment->strand }}) @endif
                    @if($latestEnrollment->section) · {{ $latestEnrollment->section->section_name }} @endif
                @endif
            </div>
        </div>
        <a href="{{ route('parent.attendance') }}" class="btn btn-light fw-bold rounded-pill px-4">
            <i class="bi bi-calendar-check me-1"></i> Full Attendance Sheet
        </a>
    </div>

    {{-- Summary Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#15803d,#16a34a);">
                <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-check-circle text-white"></i></div>
                <div>
                    <div class="stat-val text-white">{{ $attendanceSummary['Present'] }}</div>
                    <div class="stat-label">Days Present</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#a16207,#ca8a04);">
                <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-clock-history text-white"></i></div>
                <div>
                    <div class="stat-val text-white">{{ $attendanceSummary['Late'] }}</div>
                    <div class="stat-label">Days Late</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
                <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-x-circle text-white"></i></div>
                <div>
                    <div class="stat-val text-white">{{ $attendanceSummary['Absent'] }}</div>
                    <div class="stat-label">Days Absent</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-cash-coin text-white"></i></div>
                <div>
                    <div class="stat-val text-white">₱{{ number_format($totalBalance, 0) }}</div>
                    <div class="stat-label">Remaining Balance</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Recent Attendance --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance</span>
                    <a href="{{ route('parent.attendance') }}" class="small text-decoration-none">View All →</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Status</th>
                                <th>Time In</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $att)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $att->attendance_date->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $badge = match($att->status) {
                                            'Present' => 'bg-success-subtle text-success',
                                            'Late'    => 'bg-warning-subtle text-warning',
                                            'Absent'  => 'bg-danger-subtle text-danger',
                                            default   => 'bg-info-subtle text-info',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $badge }}">{{ $att->status }}</span>
                                </td>
                                <td>{{ $att->time_in ?? '—' }}</td>
                                <td class="text-muted small">{{ $att->remarks ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No attendance records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Grades & Payment Summary --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-award me-2 text-primary"></i>Published Grades</span>
                    <a href="{{ route('parent.grades') }}" class="small text-decoration-none">View All →</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Subject</th>
                                <th>Quarter</th>
                                <th class="text-end pe-3">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentGrades as $g)
                            <tr>
                                <td class="ps-3">{{ $g->subject?->subject_name ?? 'Subject' }}</td>
                                <td>Q{{ $g->quarter }}</td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($g->grade, 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No published grades yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
