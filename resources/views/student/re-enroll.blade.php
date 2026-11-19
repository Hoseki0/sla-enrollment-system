@extends('layouts.student')
@section('title', 'Online Re-Enrollment')

@section('content')
<div class="section-header">
    <div>
        <h2>Online Enrollment & Re-Enrollment</h2>
        <p>Enroll for the upcoming School Year (SY {{ $sy?->year_label ?? 'N/A' }}).</p>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

@if(!$isOpen)
<div class="card p-5 text-center mb-4 border-0 shadow-sm" style="background:#fff;">
    <div class="fs-1 text-warning mb-3"><i class="bi bi-calendar-x-fill"></i></div>
    <h4 class="fw-bold" style="color:var(--primary-dark);">Online Enrollment is Currently Closed</h4>
    <p class="text-muted mb-4">{{ $notice ?? 'Enrollment is not accepting applications right now. Please contact the registrar office for inquiries.' }}</p>
    <div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 rounded-pill">Return to Dashboard</a>
    </div>
</div>
@elseif($activeEnrollment)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 text-center">
        <div class="mb-3">
            <span class="badge rounded-pill px-3 py-2 fs-6 {{ $activeEnrollment->status_badge_class }}">
                <i class="bi bi-patch-check-fill me-1"></i> Status: {{ $activeEnrollment->status }}
            </span>
        </div>
        <h4 class="fw-bold" style="color:var(--primary-dark);">You are Registered for SY {{ $sy?->year_label }}</h4>
        <p class="text-muted mb-3">
            Enrollment No: <strong>{{ $activeEnrollment->enrollment_no }}</strong> &nbsp;|&nbsp;
            Grade: <strong>{{ $activeEnrollment->grade_level }}</strong>
            @if($activeEnrollment->strand) ({{ $activeEnrollment->strand }}) @endif
            @if($activeEnrollment->section) &nbsp;|&nbsp; Section: <strong>{{ $activeEnrollment->section->section_name }}</strong> @endif
        </p>
        <div class="d-flex justify-content-center gap-2">
            @if($activeEnrollment->status === 'Enrolled')
            <a href="{{ route('student.certificate') }}" class="btn text-white rounded-pill px-4" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                <i class="bi bi-award me-1"></i> View Certificate of Enrollment
            </a>
            @endif
            <a href="{{ route('student.payments.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-credit-card me-1"></i> View Payments
            </a>
        </div>
    </div>
</div>
@else
<div class="row g-4">
    {{-- Left: Student Details Summary --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person-badge me-2 text-primary"></i>Student Profile</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#1a3c6e,#2356a0);color:#fff;font-size:1.4rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;">
                        {{ $student->initials }}
                    </div>
                    <h5 class="fw-bold mb-0" style="color:var(--primary-dark);">{{ $student->full_name }}</h5>
                    <div class="text-muted" style="font-size:.82rem;">LRN: {{ $student->lrn ?? 'N/A' }} | ID: {{ $student->student_id }}</div>
                </div>

                <hr>

                <div class="mb-2" style="font-size:.85rem;">
                    <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Previous Level / Grade:</div>
                    <div class="fw-semibold">{{ $prevEnrollment ? $prevEnrollment->grade_level . ' (' . $prevEnrollment->schoolYear->year_label . ')' : 'First Time Enrollee' }}</div>
                </div>

                <div class="mb-2" style="font-size:.85rem;">
                    <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Contact Number:</div>
                    <div>{{ $student->contact_number ?? 'Not provided' }}</div>
                </div>

                <div class="mb-3" style="font-size:.85rem;">
                    <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;font-weight:700;">Home Address:</div>
                    <div>{{ $student->address ?? 'Not provided' }}</div>
                </div>

                <a href="{{ route('student.profile.edit') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-pencil me-1"></i> Update Contact Info
                </a>
            </div>
        </div>
    </div>

    {{-- Right: Re-Enrollment Form --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-white" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                <i class="bi bi-journal-check me-2"></i>Select Grade & Academic Track for SY {{ $sy?->year_label }}
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.re-enroll.post') }}" id="reEnrollForm" novalidate>
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Enrollment Type <span class="text-danger">*</span></label>
                            <select name="enrollment_type" class="form-select" required>
                                <option value="Old" selected>Regular / Continuing Student (Old)</option>
                                <option value="Returnee">Returning Student (Returnee)</option>
                                <option value="Transferee">Transfer Student (Transferee)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Academic Level <span class="text-danger">*</span></label>
                            <select name="level_type" id="level_type" class="form-select" required onchange="updateGradeLevels()">
                                <option value="">-- Select Level --</option>
                                <option value="JHS">Junior High School (JHS)</option>
                                <option value="SHS">Senior High School (SHS)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Grade Level to Enroll <span class="text-danger">*</span></label>
                            <select name="grade_level" id="grade_level" class="form-select" required onchange="loadSections()">
                                <option value="">-- Select Grade Level --</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="trackDiv" style="display:none;">
                            <label class="form-label fw-semibold">Senior High Track <span class="text-danger">*</span></label>
                            <select name="track" id="track" class="form-select" onchange="updateStrands()">
                                <option value="">-- Select Track --</option>
                                @foreach(array_keys($shsTracks) as $tr)
                                <option value="{{ $tr }}">{{ $tr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6" id="strandDiv" style="display:none;">
                            <label class="form-label fw-semibold">Senior High Strand <span class="text-danger">*</span></label>
                            <select name="strand" id="strand" class="form-select" onchange="loadSections()">
                                <option value="">-- Select Strand --</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="semDiv" style="display:none;">
                            <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-select">
                                <option value="1st Semester" selected>1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Preferred Section <span class="text-muted fw-normal">(Optional)</span></label>
                        <select name="section_id" id="section_id" class="form-select">
                            <option value="">-- Let Registrar Assign Section --</option>
                        </select>
                    </div>

                    <div class="p-3 rounded-3 mb-4" style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                            <div style="font-size:.83rem;color:#1e40af;">
                                Once submitted, your enrollment request will be reviewed by the Registrar. You will receive immediate access to the assigned section and tuition fee schedules upon approval.
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn text-white w-100 fw-semibold py-2 rounded-pill"
                            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                        <i class="bi bi-send-check me-1"></i> Submit Enrollment Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const shsTracks = @json($shsTracks);
const apiUrl    = "{{ route('student.enroll.sections') }}";
const syId      = "{{ $sy?->id }}";

function updateGradeLevels() {
    const lt = document.getElementById('level_type').value;
    const gl = document.getElementById('grade_level');
    gl.innerHTML = '<option value="">-- Select Grade Level --</option>';
    const levels = lt === 'JHS' ? ['Grade 7','Grade 8','Grade 9','Grade 10'] : lt === 'SHS' ? ['Grade 11','Grade 12'] : [];
    levels.forEach(l => { const o = document.createElement('option'); o.value = o.textContent = l; gl.appendChild(o); });
    const isSHS = lt === 'SHS';
    ['trackDiv','strandDiv','semDiv'].forEach(id => document.getElementById(id).style.display = isSHS ? '' : 'none');
    document.getElementById('section_id').innerHTML = '<option value="">-- Let Registrar Assign Section --</option>';
}

function updateStrands() {
    const track = document.getElementById('track').value;
    const sel = document.getElementById('strand');
    sel.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsTracks[track] || []).forEach(s => { const o = document.createElement('option'); o.value = o.textContent = s; sel.appendChild(o); });
    loadSections();
}

function loadSections() {
    const grade  = document.getElementById('grade_level').value;
    const strand = document.getElementById('strand') ? document.getElementById('strand').value : '';
    const sel    = document.getElementById('section_id');
    if (!grade) return;
    sel.innerHTML = '<option value="">Loading sections...</option>';
    fetch(`${apiUrl}?grade=${encodeURIComponent(grade)}&sy=${syId}&strand=${encodeURIComponent(strand)}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Let Registrar Assign Section --</option>';
            data.forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.section_name + (s.strand ? ` (${s.strand})` : ''); sel.appendChild(o); });
        });
}

document.getElementById('reEnrollForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting Application...';
    btn.disabled = true;
});
</script>
@endpush
