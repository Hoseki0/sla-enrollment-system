@extends('layouts.admin')
@section('title', 'Attendance Sheet')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-0">Daily Attendance Sheet</h4>
        <div class="text-muted small">SY {{ $sy?->year_label ?? 'N/A' }} &bull; Record student attendance for Parent Portal monitoring</div>
    </div>
    <form method="GET" action="{{ route('admin.attendance.index') }}" class="d-flex flex-wrap gap-2">
        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" style="width:auto;">
        <select name="grade_level" class="form-select form-select-sm" style="width:auto;">
            <option value="">All Grade Levels</option>
            @foreach($grades as $g)
                <option value="{{ $g }}" {{ $grade === $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Load</button>
    </form>
</div>

<div class="card">
    <form method="POST" action="{{ route('admin.attendance.store') }}">
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $date }}">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Student</th>
                            <th>LRN</th>
                            <th>Grade &amp; Section</th>
                            <th>Status</th>
                            <th>Time In</th>
                            <th class="pe-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $i => $enr)
                        @php $rec = $existing->get($enr->student_id); @endphp
                        <tr>
                            <td class="ps-3 fw-semibold">
                                {{ $enr->student->full_name }}
                                <input type="hidden" name="records[{{ $i }}][student_id]" value="{{ $enr->student_id }}">
                                <input type="hidden" name="records[{{ $i }}][enrollment_id]" value="{{ $enr->id }}">
                            </td>
                            <td class="small text-muted">{{ $enr->student->lrn }}</td>
                            <td class="small">{{ $enr->grade_level }} {{ $enr->strand }} · {{ $enr->section?->section_name ?? '—' }}</td>
                            <td>
                                <select name="records[{{ $i }}][status]" class="form-select form-select-sm">
                                    @foreach(['Present', 'Late', 'Absent', 'Excused'] as $st)
                                        <option value="{{ $st }}" {{ ($rec?->status ?? 'Present') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="records[{{ $i }}][time_in]" value="{{ $rec?->time_in ?? '07:30 AM' }}" class="form-control form-control-sm" style="width:110px;">
                            </td>
                            <td class="pe-3">
                                <input type="text" name="records[{{ $i }}][remarks]" value="{{ $rec?->remarks }}" placeholder="Optional note" class="form-control form-control-sm">
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No enrolled students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($enrollments->count() > 0)
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary fw-semibold">
                <i class="bi bi-save me-1"></i> Save Attendance for {{ $date }}
            </button>
        </div>
        @endif
    </form>
</div>
@endsection
