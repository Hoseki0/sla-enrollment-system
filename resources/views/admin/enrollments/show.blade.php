@extends('layouts.admin')
@section('title', 'Enrollment Details')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-journal-text me-2 text-primary"></i>Enrollment Details</h2>
        <p>{{ $enrollment->enrollment_no }}</p>
    </div>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3 mb-4">
    {{-- Enrollment Info Card --}}
    <div class="col-lg-4">
        <div class="card h-100" style="border-radius:18px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#1a3c6e,#2356a0);padding:1.5rem;">
                <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#e8a020,#f5c04a);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#1a1a1a;margin-bottom:1rem;">
                    {{ $enrollment->student->initials }}
                </div>
                <div style="color:#fff;font-size:1.1rem;font-weight:800;">{{ $enrollment->student->full_name }}</div>
                <div style="color:rgba(255,255,255,.6);font-size:.82rem;">{{ $enrollment->student->student_id }}</div>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="font-size:.85rem;">
                    <tr><td class="text-muted fw-semibold" style="width:45%;">Enrollment No.</td><td class="fw-bold text-primary">{{ $enrollment->enrollment_no }}</td></tr>
                    <tr><td class="text-muted fw-semibold">School Year</td><td>SY {{ $enrollment->schoolYear->year_label }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Level</td><td><span class="badge rounded-pill px-2 {{ $enrollment->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $enrollment->level_type }}</span></td></tr>
                    <tr><td class="text-muted fw-semibold">Grade</td><td>{{ $enrollment->grade_level }}</td></tr>
                    @if($enrollment->track)<tr><td class="text-muted fw-semibold">Track</td><td>{{ $enrollment->track }}</td></tr>@endif
                    @if($enrollment->strand)<tr><td class="text-muted fw-semibold">Strand</td><td>{{ $enrollment->strand }}</td></tr>@endif
                    @if($enrollment->semester)<tr><td class="text-muted fw-semibold">Semester</td><td>{{ $enrollment->semester }}</td></tr>@endif
                    <tr><td class="text-muted fw-semibold">Section</td><td>{{ $enrollment->section?->section_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Type</td><td>{{ $enrollment->enrollment_type }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Date</td><td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td></tr>
                    <tr>
                        <td class="text-muted fw-semibold">Status</td>
                        <td><span class="badge rounded-pill px-2 {{ $enrollment->status_badge_class }}">{{ $enrollment->status }}</span></td>
                    </tr>
                    <tr><td class="text-muted fw-semibold">Encoded By</td><td>{{ $enrollment->encodedBy?->name ?? '—' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Update Status --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-pencil me-2"></i>Update Enrollment Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.enrollments.updateStatus', $enrollment) }}" class="d-flex gap-2 align-items-end">
                    @csrf @method('PATCH')
                    <div class="flex-grow-1">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['Pending','Enrolled','Cancelled','Dropped'] as $st)
                            <option value="{{ $st }}" {{ $enrollment->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background:#1a3c6e;">
                        <i class="bi bi-check2 me-1"></i>Update
                    </button>
                </form>
            </div>
        </div>

        {{-- Student Info --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Student Information</div>
            <div class="card-body">
                <div class="row g-2" style="font-size:.88rem;">
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">LRN</span>{{ $enrollment->student->lrn ?? '—' }}</div>
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">GENDER</span>{{ $enrollment->student->gender }}</div>
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">BIRTHDATE</span>{{ $enrollment->student->birthdate?->format('M d, Y') ?? '—' }}</div>
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">RELIGION</span>{{ $enrollment->student->religion ?? '—' }}</div>
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">CONTACT</span>{{ $enrollment->student->contact_number ?? '—' }}</div>
                    <div class="col-md-4"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">EMAIL</span>{{ $enrollment->student->email ?? '—' }}</div>
                    <div class="col-12"><span class="text-muted d-block" style="font-size:.75rem;font-weight:600;">ADDRESS</span>{{ implode(', ', array_filter([$enrollment->student->address, $enrollment->student->barangay, $enrollment->student->municipality, $enrollment->student->province])) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('admin.students.show', $enrollment->student) }}" class="btn btn-outline-primary rounded-pill px-3">
        <i class="bi bi-person me-1"></i>View Full Student Profile
    </a>
</div>
@endsection
