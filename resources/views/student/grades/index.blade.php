@extends('layouts.student')
@section('title', 'My Grades')

@push('styles')
<style>
    .grade-table th { font-size:.75rem; text-align:center; }
    .grade-table td { text-align:center; font-weight:600; }
    .grade-pass { color:#15803d; }
    .grade-fail { color:#dc2626; }
    .quarter-avg { background:#f0fdf4; font-weight:800; color:#15803d; }
</style>
@endpush

@section('content')
<div class="section-header">
    <div>
        <h2>My Grades</h2>
        <p class="text-muted mb-0">Academic performance report — SY {{ $sy?->year_label ?? 'N/A' }}</p>
    </div>
    @if($enrollment)
    <span class="badge rounded-pill px-3 py-2 {{ $enrollment->level_type==='JHS'?'badge-jhs':'badge-shs' }}">
        {{ $enrollment->grade_level }} @if($enrollment->strand)· {{ $enrollment->strand }}@endif
    </span>
    @endif
</div>

@if(!$enrollment)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-mortarboard fs-1 text-muted opacity-25 d-block mb-3"></i>
        <h5>No Active Enrollment</h5>
        <p class="text-muted">Grades will appear here once you are enrolled for the current school year.</p>
    </div>
</div>
@else
@if($generalAverage)
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-star text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ number_format($generalAverage, 2) }}</div>
                <div class="stat-label">General Average</div>
            </div>
        </div>
    </div>
    @foreach([1,2,3,4] as $q)
    <div class="col-md-2 col-6">
        <div class="stat-card" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);">
            <div>
                <div class="stat-val text-white" style="font-size:1.2rem;">{{ $averages[$q] ? number_format($averages[$q], 2) : '—' }}</div>
                <div class="stat-label">Q{{ $q }} Avg</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-table" style="color:#1a3c6e;"></i>
        <span>Grade Report Card</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 grade-table">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3 text-start">Subject</th>
                        <th>Q1</th>
                        <th>Q2</th>
                        <th>Q3</th>
                        <th>Q4</th>
                        <th>Final</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($subjects as $subject)
                @php
                    $subjectGrades = $gradeMap->get($subject->id, collect());
                    $qGrades = [];
                    for ($q = 1; $q <= 4; $q++) {
                        $qGrades[$q] = $subjectGrades->firstWhere('quarter', $q)?->grade;
                    }
                    $validGrades = array_filter($qGrades, fn($g) => $g !== null);
                    $final = count($validGrades) > 0 ? round(array_sum($validGrades) / count($validGrades), 2) : null;
                @endphp
                <tr>
                    <td class="ps-3 text-start">
                        <div style="font-size:.85rem;font-weight:600;">{{ $subject->subject_name }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $subject->subject_code }}</div>
                    </td>
                    @foreach([1,2,3,4] as $q)
                    <td class="{{ $qGrades[$q] !== null ? ($qGrades[$q] >= 75 ? 'grade-pass' : 'grade-fail') : '' }}">
                        {{ $qGrades[$q] !== null ? number_format($qGrades[$q], 0) : '—' }}
                    </td>
                    @endforeach
                    <td class="fw-bold {{ $final !== null ? ($final >= 75 ? 'grade-pass' : 'grade-fail') : '' }}">
                        {{ $final !== null ? number_format($final, 2) : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No subjects found for your grade level.</td></tr>
                @endforelse
                @if($subjects->count() > 0)
                <tr class="quarter-avg">
                    <td class="ps-3 text-start fw-bold">Quarter Average</td>
                    @foreach([1,2,3,4] as $q)
                    <td>{{ $averages[$q] ? number_format($averages[$q], 2) : '—' }}</td>
                    @endforeach
                    <td>{{ $generalAverage ? number_format($generalAverage, 2) : '—' }}</td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body" style="font-size:.82rem;">
        <strong>Grading Scale:</strong>
        90–100 Outstanding · 85–89 Very Satisfactory · 80–84 Satisfactory · 75–79 Fairly Satisfactory · Below 75 Did Not Meet Expectations
    </div>
</div>
@endif
@endsection
