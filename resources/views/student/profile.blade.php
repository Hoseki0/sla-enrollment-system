@extends('layouts.student')
@section('title', 'My Profile')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-person-circle me-2" style="color:#15803d;"></i>My Profile</h2>
        <p style="color:#64748b;font-size:.85rem;margin:2px 0 0;">Your personal information on record.</p>
    </div>
</div>

{{-- ID Card --}}
<div class="card mb-4" style="overflow:hidden;">
    <div style="background:linear-gradient(135deg,#15803d,#166534);padding:1.75rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#e8a020,#f5c04a);display:flex;align-items:center;justify-content:center;font-size:1.9rem;font-weight:800;color:#1a1a1a;flex-shrink:0;">
            {{ $student->initials }}
        </div>
        <div class="flex-grow-1">
            <div style="color:#fff;font-size:1.35rem;font-weight:800;line-height:1.2;">{{ $student->full_name }}</div>
            <div style="color:rgba(255,255,255,.6);font-size:.85rem;margin-top:.25rem;">
                LRN: <span style="color:#f5c04a;font-weight:700;">{{ $student->lrn ?? '—' }}</span>
                &nbsp;&bull;&nbsp; Student ID: <span style="color:#f5c04a;font-weight:700;">{{ $student->student_id }}</span>
            </div>
        </div>
        <div class="text-end" style="color:rgba(255,255,255,.6);font-size:.78rem;">
            <div>{{ $student->gender }}</div>
            <div>{{ $student->age }} years old</div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:.88rem;">
            <div class="col-md-3"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Birthdate</span>{{ $student->birthdate?->format('M d, Y') ?? '—' }}</div>
            <div class="col-md-3"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Civil Status</span>{{ $student->civil_status }}</div>
            <div class="col-md-3"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Religion</span>{{ $student->religion ?? '—' }}</div>
            <div class="col-md-3"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Nationality</span>{{ $student->nationality }}</div>
            <div class="col-md-4"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Email</span>{{ $student->email ?? '—' }}</div>
            <div class="col-md-4"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Contact No.</span>{{ $student->contact_number ?? '—' }}</div>
            <div class="col-md-4"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">School Type</span>{{ $student->school_type }}</div>
            <div class="col-12"><span class="text-muted d-block fw-semibold" style="font-size:.72rem;text-transform:uppercase;">Address</span>
                {{ implode(', ', array_filter([$student->address, $student->barangay, $student->municipality, $student->province, $student->zip_code])) }}
            </div>
        </div>
    </div>
</div>

{{-- Parents & Guardian --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="font-size:.88rem;"><i class="bi bi-person me-2 text-primary"></i>Father</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->father_name ?? '—' }}</div>
                @if($student->father_occupation)<div class="text-muted" style="font-size:.82rem;">{{ $student->father_occupation }}</div>@endif
                @if($student->father_contact)<div><i class="bi bi-telephone me-1 text-muted" style="font-size:.78rem;"></i>{{ $student->father_contact }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="font-size:.88rem;"><i class="bi bi-person me-2 text-primary"></i>Mother</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->mother_name ?? '—' }}</div>
                @if($student->mother_occupation)<div class="text-muted" style="font-size:.82rem;">{{ $student->mother_occupation }}</div>@endif
                @if($student->mother_contact)<div><i class="bi bi-telephone me-1 text-muted" style="font-size:.78rem;"></i>{{ $student->mother_contact }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="font-size:.88rem;"><i class="bi bi-person-heart me-2 text-primary"></i>Guardian</div>
            <div class="card-body" style="font-size:.88rem;">
                <div class="fw-semibold">{{ $student->guardian_name ?? '—' }}</div>
                @if($student->guardian_relationship)<div class="text-muted" style="font-size:.82rem;">{{ $student->guardian_relationship }}</div>@endif
                @if($student->guardian_contact)<div><i class="bi bi-telephone me-1 text-muted" style="font-size:.78rem;"></i>{{ $student->guardian_contact }}</div>@endif
            </div>
        </div>
    </div>
</div>

{{-- Previous School --}}
@if($student->last_school_attended)
<div class="card">
    <div class="card-header" style="font-size:.88rem;"><i class="bi bi-building me-2 text-primary"></i>Previous School Attended</div>
    <div class="card-body" style="font-size:.88rem;">
        <div class="fw-semibold">{{ $student->last_school_attended }}</div>
        <div class="text-muted">{{ $student->last_school_address }}</div>
        <span class="badge rounded-pill mt-1 px-3" style="background:#eff6ff;color:#1d4ed8;font-size:.75rem;">{{ $student->school_type }}</span>
    </div>
</div>
@endif
@endsection
