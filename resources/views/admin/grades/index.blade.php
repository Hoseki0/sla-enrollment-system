@extends('layouts.admin')
@section('title', 'Student Grades')

@section('content')
<div class="section-header">
    <div>
        <h2>Student Grades</h2>
        <p>Encode and manage student grades — SY {{ $sy?->year_label ?? 'N/A' }}</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter by Grade Level</label>
                <select name="grade_level" class="form-select" onchange="this.form.submit()">
                    <option value="">All Grades</option>
                    @foreach($gradeLevels as $gl)
                    <option value="{{ $gl }}" {{ request('grade_level')===$gl?'selected':'' }}>{{ $gl }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 datatable">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Student ID</th>
                        <th>Name</th>
                        <th>Grade Level</th>
                        <th>Strand</th>
                        <th>Section</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($enrollments as $e)
                <tr>
                    <td class="ps-3" style="font-size:.82rem;font-weight:600;">{{ $e->student->student_id }}</td>
                    <td>{{ $e->student->full_name }}</td>
                    <td>{{ $e->grade_level }}</td>
                    <td>{{ $e->strand ?? '—' }}</td>
                    <td>{{ $e->section?->section_name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.grades.show', $e) }}" class="btn btn-sm btn-primary rounded-3" style="background:var(--primary);border:none;">
                            <i class="bi bi-pencil-square me-1"></i> Encode Grades
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
