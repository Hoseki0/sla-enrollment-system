@extends('layouts.admin')
@section('title', 'New Enrollment')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-person-plus me-2 text-primary"></i>New Enrollment</h2>
        <p>Fill in the student information to process enrollment.</p>
    </div>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($errors->any())
<div class="alert border-0 rounded-3 mb-4" style="background:#fee2e2;color:#dc2626;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1 ps-4">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.enrollments.store') }}" id="enrollForm" novalidate>
    @csrf

    {{-- ENROLLMENT INFO --}}
    <div class="card mb-4">
        <div class="card-header text-white" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:15px 15px 0 0;">
            <i class="bi bi-journal-check me-2"></i>Enrollment Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">School Year <span class="text-danger">*</span></label>
                    <select name="school_year_id" id="school_year_id" class="form-select" required>
                        @foreach($schoolYears as $s)
                        <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>
                            SY {{ $s->year_label }} {{ $s->is_active ? '(Active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Enrollment Type <span class="text-danger">*</span></label>
                    <select name="enrollment_type" class="form-select" required>
                        @foreach(['New','Old','Transferee','Returnee'] as $t)
                        <option value="{{ $t }}" {{ old('enrollment_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Level Type <span class="text-danger">*</span></label>
                    <select name="level_type" id="level_type" class="form-select" required onchange="updateGradeLevels()">
                        <option value="">-- Select Level --</option>
                        <option value="JHS" {{ old('level_type') === 'JHS' ? 'selected' : '' }}>Junior High School (JHS)</option>
                        <option value="SHS" {{ old('level_type') === 'SHS' ? 'selected' : '' }}>Senior High School (SHS)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                    <select name="grade_level" id="grade_level" class="form-select" required onchange="loadSections()">
                        <option value="">-- Select Grade --</option>
                    </select>
                </div>
                <div class="col-md-3" id="trackDiv" style="display:none;">
                    <label class="form-label">Track <span class="text-danger">*</span></label>
                    <select name="track" id="track" class="form-select" onchange="updateStrands()">
                        <option value="">-- Select Track --</option>
                        @foreach(array_keys($shsTracks) as $track)
                        <option value="{{ $track }}">{{ $track }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3" id="strandDiv" style="display:none;">
                    <label class="form-label">Strand <span class="text-danger">*</span></label>
                    <select name="strand" id="strand" class="form-select" onchange="loadSections()">
                        <option value="">-- Select Strand --</option>
                    </select>
                </div>
                <div class="col-md-3" id="semDiv" style="display:none;">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <select name="semester" id="semester" class="form-select">
                        <option value="">-- Select --</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select name="section_id" id="section_id" class="form-select">
                        <option value="">-- Select Section (Optional) --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- PERSONAL INFO --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;">
            <i class="bi bi-person me-2 text-primary"></i>Personal Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}" placeholder="Dela Cruz">
                </div>
                <div class="col-md-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}" placeholder="Juan">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Suffix</label>
                    <input type="text" name="suffix" class="form-control" value="{{ old('suffix') }}" placeholder="Jr." maxlength="10">
                </div>
                <div class="col-md-2">
                    <label class="form-label">LRN</label>
                    <input type="text" name="lrn" class="form-control" maxlength="12" value="{{ old('lrn') }}" placeholder="12-digit LRN">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="birthdate" id="birthdate" class="form-control" required value="{{ old('birthdate') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Age</label>
                    <input type="text" id="age_display" class="form-control" readonly placeholder="Auto">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">--</option>
                        <option value="Male" {{ old('gender')==='Male'?'selected':'' }}>Male</option>
                        <option value="Female" {{ old('gender')==='Female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Civil Status</label>
                    <select name="civil_status" class="form-select">
                        @foreach(['Single','Married','Widowed'] as $cs)
                        <option value="{{ $cs }}" {{ old('civil_status','Single')===$cs?'selected':'' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Religion</label>
                    <input type="text" name="religion" class="form-control" value="{{ old('religion') }}" placeholder="Roman Catholic">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality','Filipino') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="student@email.com">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX">
                </div>
            </div>
        </div>
    </div>

    {{-- ADDRESS --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;">
            <i class="bi bi-geo-alt me-2 text-primary"></i>Address
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Street / House No. <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="123 Mabini St.">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Barangay</label>
                    <input type="text" name="barangay" class="form-control" value="{{ old('barangay') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Municipality / City</label>
                    <input type="text" name="municipality" class="form-control" value="{{ old('municipality') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Province</label>
                    <input type="text" name="province" class="form-control" value="{{ old('province') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip_code" class="form-control" maxlength="10" value="{{ old('zip_code') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- PARENTS / GUARDIAN --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;">
            <i class="bi bi-house-heart me-2 text-primary"></i>Parents / Guardian Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">FATHER</p></div>
                <div class="col-md-4"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}"></div>
                <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation') }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="father_contact" class="form-control" value="{{ old('father_contact') }}"></div>

                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">MOTHER</p></div>
                <div class="col-md-4"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}"></div>
                <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation') }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="mother_contact" class="form-control" value="{{ old('mother_contact') }}"></div>

                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">GUARDIAN (if different)</p></div>
                <div class="col-md-4"><label class="form-label">Guardian's Name</label><input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}"></div>
                <div class="col-md-3"><label class="form-label">Relationship</label><input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship') }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact') }}"></div>
            </div>
        </div>
    </div>

    {{-- PREVIOUS SCHOOL --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;">
            <i class="bi bi-building me-2 text-primary"></i>Previous School Attended
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5"><label class="form-label">School Name</label><input type="text" name="last_school_attended" class="form-control" value="{{ old('last_school_attended') }}"></div>
                <div class="col-md-5"><label class="form-label">School Address</label><input type="text" name="last_school_address" class="form-control" value="{{ old('last_school_address') }}"></div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="school_type" class="form-select">
                        <option value="Public" {{ old('school_type','Public')==='Public'?'selected':'' }}>Public</option>
                        <option value="Private" {{ old('school_type')==='Private'?'selected':'' }}>Private</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <button type="reset" class="btn btn-outline-secondary px-4 rounded-pill"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
        <button type="submit" id="submitBtn" class="btn text-white px-5 rounded-pill fw-semibold" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <i class="bi bi-person-check me-2"></i>Submit Enrollment
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const shsTracks = @json($shsTracks);
const apiUrl    = "{{ route('admin.api.sections') }}";

function updateGradeLevels() {
    const lt = document.getElementById('level_type').value;
    const gl = document.getElementById('grade_level');
    gl.innerHTML = '<option value="">-- Select Grade --</option>';
    const levels = lt === 'JHS' ? ['Grade 7','Grade 8','Grade 9','Grade 10'] : lt === 'SHS' ? ['Grade 11','Grade 12'] : [];
    levels.forEach(l => { const o = document.createElement('option'); o.value = o.textContent = l; gl.appendChild(o); });
    const isSHS = lt === 'SHS';
    ['trackDiv','strandDiv','semDiv'].forEach(id => document.getElementById(id).style.display = isSHS ? '' : 'none');
    document.getElementById('section_id').innerHTML = '<option value="">-- Select Section (Optional) --</option>';
}

function updateStrands() {
    const track = document.getElementById('track').value;
    const sel = document.getElementById('strand');
    sel.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsTracks[track] || []).forEach(s => { const o = document.createElement('option'); o.value = o.textContent = s; sel.appendChild(o); });
    loadSections();
}

function loadSections() {
    const grade = document.getElementById('grade_level').value;
    const sy    = document.getElementById('school_year_id').value;
    const strand= document.getElementById('strand') ? document.getElementById('strand').value : '';
    const sel   = document.getElementById('section_id');
    if (!grade) { sel.innerHTML = '<option value="">-- Select Grade First --</option>'; return; }
    sel.innerHTML = '<option value="">-- Loading... --</option>';
    fetch(`${apiUrl}?grade=${encodeURIComponent(grade)}&sy=${sy}&strand=${encodeURIComponent(strand)}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Select Section (Optional) --</option>';
            data.forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.section_name + (s.strand ? ` (${s.strand})` : ''); sel.appendChild(o); });
        });
}

document.getElementById('birthdate').addEventListener('change', function () {
    const age = Math.floor((new Date() - new Date(this.value)) / (365.25 * 24 * 3600 * 1000));
    document.getElementById('age_display').value = age > 0 ? age + ' yrs old' : '';
});

document.getElementById('strand').addEventListener('change', loadSections);
document.getElementById('school_year_id').addEventListener('change', loadSections);
document.getElementById('enrollForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    btn.disabled = true;
});
</script>
@endpush
