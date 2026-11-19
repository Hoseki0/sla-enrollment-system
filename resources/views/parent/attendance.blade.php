@extends('layouts.parent')
@section('title', 'Attendance Sheet')

@section('content')
@if(!$child)
    <div class="alert alert-warning">Please connect a child account first.</div>
@else
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-0">Attendance Sheet — {{ $child->full_name }}</h4>
            <div class="text-muted small">LRN: {{ $child->lrn }} &bull; SY {{ $sy?->year_label ?? 'N/A' }}</div>
        </div>
        <form method="GET" action="{{ route('parent.attendance') }}" class="d-flex align-items-center gap-2">
            <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if($month)
                <a href="{{ route('parent.attendance') }}" class="btn btn-light btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="text-success fw-bold fs-4">{{ $summary['Present'] }}</div>
                <div class="text-muted small">Present</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="text-warning fw-bold fs-4">{{ $summary['Late'] }}</div>
                <div class="text-muted small">Late</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="text-danger fw-bold fs-4">{{ $summary['Absent'] }}</div>
                <div class="text-muted small">Absent</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="text-info fw-bold fs-4">{{ $summary['Excused'] }}</div>
                <div class="text-muted small">Excused</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daily Attendance Log</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Day</th>
                            <th>Status</th>
                            <th>Time In</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                        @php
                            $badge = match($rec->status) {
                                'Present' => 'bg-success-subtle text-success',
                                'Late'    => 'bg-warning-subtle text-warning',
                                'Absent'  => 'bg-danger-subtle text-danger',
                                default   => 'bg-info-subtle text-info',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $rec->attendance_date->format('M d, Y') }}</td>
                            <td class="text-muted">{{ $rec->attendance_date->format('l') }}</td>
                            <td><span class="badge rounded-pill px-3 {{ $badge }}">{{ $rec->status }}</span></td>
                            <td>{{ $rec->time_in ?? '—' }}</td>
                            <td class="text-muted">{{ $rec->remarks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No attendance records found for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
