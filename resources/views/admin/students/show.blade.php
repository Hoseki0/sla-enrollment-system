@extends('layouts.admin')
@section('title', 'Student Profile')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-person-circle me-2 text-primary"></i>Student Profile</h2>
        <p>{{ $student->full_name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

{{-- ID Card --}}
<div class="card mb-4" style="border-radius:18px;overflow:hidden;">
    <div style="background:linear-gradient(135deg,#1a3c6e,#2356a0);padding:1.5rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
        <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#e8a020,#f5c04a);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:800;color:#1a1a1a;flex-shrink:0;">
            {{ $student->initials }}
        </div>
        <div class="flex-grow-1">
            <div style="color:#fff;font-size:1.35rem;font-weight:800;line-height:1.2;">{{ $student->full_name }}</div>
            <div style="color:rgba(255,255,255,.6);font-size:.85rem;">
                Student ID: <span style="color:#f5c04a;font-weight:700;">{{ $student->student_id }}</span>
            </div>
        </div>
        <div class="text-end">
            <div style="color:rgba(255,255,255,.5);font-size:.72rem;text-transform:uppercase;letter-spacing:1px;">LRN</div>
            <div style="color:#fff;font-weight:700;">{{ $student->lrn ?? '—' }}</div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:.88rem;">
            <div class="col-md-2"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">GENDER</span>{{ $student->gender }}</div>
            <div class="col-md-2"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">BIRTHDATE</span>{{ $student->birthdate?->format('M d, Y') ?? '—' }}</div>
            <div class="col-md-2"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">AGE</span>{{ $student->age }} yrs old</div>
            <div class="col-md-2"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">RELIGION</span>{{ $student->religion ?? '—' }}</div>
            <div class="col-md-4"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">EMAIL</span>{{ $student->email ?? '—' }}</div>
            <div class="col-md-3"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">CONTACT</span>{{ $student->contact_number ?? '—' }}</div>
            <div class="col-md-9"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;">ADDRESS</span>{{ implode(', ', array_filter([$student->address, $student->barangay, $student->municipality, $student->province])) }}</div>
        </div>
    </div>
</div>

{{-- Parents --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person me-2"></i>Father</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->father_name ?? '—' }}</div>
                <div class="text-muted">{{ $student->father_occupation }}</div>
                <div>{{ $student->father_contact ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person me-2"></i>Mother</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->mother_name ?? '—' }}</div>
                <div class="text-muted">{{ $student->mother_occupation }}</div>
                <div>{{ $student->mother_contact ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person-heart me-2"></i>Guardian</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->guardian_name ?? '—' }}</div>
                <div class="text-muted">{{ $student->guardian_relationship }}</div>
                <div>{{ $student->guardian_contact ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Enrollment History --}}
<div class="card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Enrollment History</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Enrollment No.</th><th>School Year</th><th>Level</th>
                        <th>Grade</th><th>Strand</th><th>Section</th><th>Type</th><th>Status</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($student->enrollments as $e)
                <tr>
                    <td class="ps-3 fw-semibold text-primary" style="font-size:.82rem;">
                        <a href="{{ route('admin.enrollments.show', $e) }}" class="text-decoration-none">{{ $e->enrollment_no }}</a>
                    </td>
                    <td style="font-size:.85rem;">SY {{ $e->schoolYear->year_label }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $e->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $e->level_type }}</span></td>
                    <td style="font-size:.85rem;">{{ $e->grade_level }}</td>
                    <td style="font-size:.82rem;">{{ $e->strand ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->section?->section_name ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->enrollment_type }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $e->status_badge_class }}">{{ $e->status }}</span></td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $e->enrollment_date->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No enrollment records yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
