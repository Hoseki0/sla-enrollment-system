@extends('layouts.admin')
@section('title', 'Enrollment Report')

@section('content')
<div class="section-header">
    <div>
        <h2>Enrollment Report</h2>
        <p>Print or export list of enrolled students.</p>
    </div>
    <button onclick="window.print()" class="btn text-white fw-semibold px-3"
            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;">
        <i class="bi bi-printer me-1"></i> Print / Save PDF
    </button>
</div>

{{-- Filters --}}
<form class="row g-2 mb-4" method="GET">
    <div class="col-auto">
        <select name="sy" class="form-select form-select-sm">
            @foreach($schoolYears as $year)
            <option value="{{ $year->id }}" {{ $syId == $year->id ? 'selected' : '' }}>SY {{ $year->year_label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="level" class="form-select form-select-sm">
            <option value="">All Levels</option>
            <option value="JHS" {{ request('level')==='JHS'?'selected':'' }}>JHS</option>
            <option value="SHS" {{ request('level')==='SHS'?'selected':'' }}>SHS</option>
        </select>
    </div>
    <div class="col-auto">
        <select name="grade" class="form-select form-select-sm">
            <option value="">All Grades</option>
            @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
            <option value="{{ $g }}" {{ request('grade')===$g?'selected':'' }}>{{ $g }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('admin.reports.enrollments') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
    </div>
</form>

{{-- Print header --}}
<div class="d-none d-print-block mb-4 text-center">
    <h4 class="fw-bold mb-0" style="color:#1a3c6e;">
        {{ \App\Models\Setting::get('school_name', 'Sta. Lucia Academy') }}
    </h4>
    <div style="font-size:.9rem;">{{ \App\Models\Setting::get('school_address', '') }}</div>
    <h5 class="mt-3 fw-bold">ENROLLMENT LIST — SY {{ $selectedSy?->year_label }}</h5>
    <div style="font-size:.85rem;">Printed: {{ now()->format('F d, Y h:i A') }}</div>
    <hr>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Enrolled Students — SY {{ $selectedSy?->year_label }}</span>
        <span class="badge rounded-pill" style="background:#eff6ff;color:#1d4ed8;">{{ $enrollments->count() }} students</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="enrollReport">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Student Name</th>
                        <th>LRN</th>
                        <th>Level</th>
                        <th>Grade</th>
                        <th>Strand</th>
                        <th>Section</th>
                        <th>Type</th>
                        <th>Date Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($enrollments as $i => $e)
                <tr>
                    <td class="ps-3 text-muted" style="font-size:.82rem;">{{ $i + 1 }}</td>
                    <td class="fw-semibold" style="font-size:.88rem;">{{ $e->student->full_name }}</td>
                    <td style="font-size:.82rem;">{{ $e->student->lrn ?? '—' }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $e->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $e->level_type }}</span></td>
                    <td style="font-size:.85rem;">{{ $e->grade_level }}</td>
                    <td style="font-size:.82rem;">{{ $e->strand ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->section?->section_name ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->enrollment_type }}</td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $e->enrollment_date->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .section-header .btn, form, .alert { display: none !important; }
    .main-content { margin: 0 !important; padding: 1rem !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table th, .table td { font-size: .8rem !important; padding: .3rem .5rem !important; }
}
</style>
@endpush
