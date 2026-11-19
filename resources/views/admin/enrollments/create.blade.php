@extends('layouts.admin')
@section('title', 'New Enrollment')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-person-plus me-2 text-primary"></i>Enrollment Processing</h2>
        <p>Enroll existing regular students or register new students and transferees.</p>
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

{{-- Student Type Tabs --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-2 bg-light rounded-3">
        <ul class="nav nav-pills nav-fill" id="enrollmentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ old('existing_student_id') || !old('first_name') ? 'active' : '' }} fw-semibold"
                        id="regular-tab" data-bs-toggle="pill" data-bs-target="#regular-pane" type="button" role="tab">
                    <i class="bi bi-person-check-fill me-2"></i>Regular / Existing Student
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ old('first_name') && !old('existing_student_id') ? 'active' : '' }} fw-semibold"
                        id="new-tab" data-bs-toggle="pill" data-bs-target="#new-pane" type="button" role="tab">
                    <i class="bi bi-person-plus-fill me-2"></i>New Student / Transferee
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="enrollmentTabContent">

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: REGULAR / EXISTING STUDENT ENROLLMENT --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div class="tab-pane fade {{ old('existing_student_id') || !old('first_name') ? 'show active' : '' }}" id="regular-pane" role="tabpanel">
        <form method="POST" action="{{ route('admin.enrollments.store') }}" id="regularForm" novalidate>
            @csrf

            <div class="card mb-4">
                <div class="card-header text-white" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                    <i class="bi bi-person-badge me-2"></i>Select Regular Student
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Student Name / LRN <span class="text-danger">*</span></label>
                            <select name="existing_student_id" id="existing_student_id" class="form-select select2" required onchange="displayStudentInfo(this)">
                                <option value="">-- Search & Select Existing Student --</option>
                                @foreach($existingStudents as $st)
                                <option value="{{ $st->id }}"
                                        data-lrn="{{ $st->lrn }}"
                                        data-gender="{{ $st->gender }}"
                                        data-last-enroll="{{ $st->latestEnrollment ? $st->latestEnrollment->grade_level . ' (' . $st->latestEnrollment->schoolYear->year_label . ')' : 'None' }}"
                                        {{ old('existing_student_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->full_name }} — LRN: {{ $st->lrn ?? 'No LRN' }} (ID: {{ $st->student_id }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="studentPreview" class="mt-3 p-3 rounded-3" style="display:none;background:#f8faff;border:1px solid #e2e8f0;">
                        <div class="row g-2" style="font-size:.88rem;">
                            <div class="col-md-4"><strong>LRN:</strong> <span id="prevLrn">—</span></div>
                            <div class="col-md-4"><strong>Gender:</strong> <span id="prevGender">—</span></div>
                            <div class="col-md-4"><strong>Previous Enrollment:</strong> <span id="prevEnroll" class="badge bg-primary bg-opacity-10 text-primary">—</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header text-white" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                    <i class="bi bi-journal-check me-2"></i>Academic Enrollment Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">School Year <span class="text-danger">*</span></label>
                            <select name="school_year_id" id="reg_school_year_id" class="form-select" required>
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
                                <option value="Old" selected>Old (Regular / Continuing Student)</option>
                                <option value="Returnee">Returnee</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Level Type <span class="text-danger">*</span></label>
                            <select name="level_type" id="reg_level_type" class="form-select" required onchange="updateRegGradeLevels()">
                                <option value="">-- Select Level --</option>
                                <option value="JHS">Junior High School (JHS)</option>
                                <option value="SHS">Senior High School (SHS)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                            <select name="grade_level" id="reg_grade_level" class="form-select" required onchange="loadRegSections()">
                                <option value="">-- Select Grade --</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="regTrackDiv" style="display:none;">
                            <label class="form-label">Track <span class="text-danger">*</span></label>
                            <select name="track" id="reg_track" class="form-select" onchange="updateRegStrands()">
                                <option value="">-- Select Track --</option>
                                @foreach(array_keys($shsTracks) as $track)
                                <option value="{{ $track }}">{{ $track }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="regStrandDiv" style="display:none;">
                            <label class="form-label">Strand <span class="text-danger">*</span></label>
                            <select name="strand" id="reg_strand" class="form-select" onchange="loadRegSections()">
                                <option value="">-- Select Strand --</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="regSemDiv" style="display:none;">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="reg_semester" class="form-select">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" id="reg_section_id" class="form-select">
                                <option value="">-- Select Section (Optional) --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn text-white px-5 rounded-pill fw-semibold" style="background:linear-gradient(135deg,#15803d,#16a34a);">
                    <i class="bi bi-check-circle me-2"></i>Enroll Regular Student
                </button>
            </div>
        </form>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: NEW STUDENT / TRANSFEREE ENROLLMENT --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div class="tab-pane fade {{ old('first_name') && !old('existing_student_id') ? 'show active' : '' }}" id="new-pane" role="tabpanel">
        <form method="POST" action="{{ route('admin.enrollments.store') }}" id="newForm" novalidate>
            @csrf

            {{-- ENROLLMENT INFO --}}
            <div class="card mb-4">
                <div class="card-header text-white" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                    <i class="bi bi-journal-check me-2"></i>Enrollment Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">School Year <span class="text-danger">*</span></label>
                            <select name="school_year_id" id="new_school_year_id" class="form-select" required>
                                @foreach($schoolYears as $s)
                                <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>
                                    SY {{ $s->year_label }} {{ $s->is_active ? '(Active)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Enrollment Type <span class="text-danger">*</span></label>
                            <select name="enrollment_type" id="new_enrollment_type" class="form-select" required onchange="toggleTransferFields()">
                                <option value="New" {{ old('enrollment_type') === 'New' ? 'selected' : '' }}>New Student</option>
                                <option value="Transferee" {{ old('enrollment_type') === 'Transferee' ? 'selected' : '' }}>Transferee (Transfer Student)</option>
                                <option value="Returnee" {{ old('enrollment_type') === 'Returnee' ? 'selected' : '' }}>Returnee</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Level Type <span class="text-danger">*</span></label>
                            <select name="level_type" id="new_level_type" class="form-select" required onchange="updateNewGradeLevels()">
                                <option value="">-- Select Level --</option>
                                <option value="JHS" {{ old('level_type') === 'JHS' ? 'selected' : '' }}>Junior High School (JHS)</option>
                                <option value="SHS" {{ old('level_type') === 'SHS' ? 'selected' : '' }}>Senior High School (SHS)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                            <select name="grade_level" id="new_grade_level" class="form-select" required onchange="loadNewSections()">
                                <option value="">-- Select Grade --</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="newTrackDiv" style="display:none;">
                            <label class="form-label">Track <span class="text-danger">*</span></label>
                            <select name="track" id="new_track" class="form-select" onchange="updateNewStrands()">
                                <option value="">-- Select Track --</option>
                                @foreach(array_keys($shsTracks) as $track)
                                <option value="{{ $track }}">{{ $track }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="newStrandDiv" style="display:none;">
                            <label class="form-label">Strand <span class="text-danger">*</span></label>
                            <select name="strand" id="new_strand" class="form-select" onchange="loadNewSections()">
                                <option value="">-- Select Strand --</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="newSemDiv" style="display:none;">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="new_semester" class="form-select">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" id="new_section_id" class="form-select">
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
                            <input type="date" name="birthdate" id="new_birthdate" class="form-control" required value="{{ old('birthdate') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Age</label>
                            <input type="text" id="new_age_display" class="form-control" readonly placeholder="Auto">
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

            {{-- PREVIOUS SCHOOL / TRANSFER CREDENTIALS --}}
            <div class="card mb-4" id="transferCard">
                <div class="card-header text-primary" style="background:#f8faff;">
                    <i class="bi bi-building me-2"></i>Previous School & Transfer Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" id="schoolNameLabel">Last School Attended</label>
                            <input type="text" name="last_school_attended" id="last_school_attended" class="form-control" value="{{ old('last_school_attended') }}" placeholder="e.g. Rizal National High School">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">School Address</label>
                            <input type="text" name="last_school_address" class="form-control" value="{{ old('last_school_address') }}" placeholder="City / Municipality">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">School Type</label>
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
                <button type="submit" id="newSubmitBtn" class="btn text-white px-5 rounded-pill fw-semibold" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
                    <i class="bi bi-person-check me-2"></i>Register & Enroll Student
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
const shsTracks = @json($shsTracks);
const apiUrl    = "{{ route('admin.api.sections') }}";

// ── REGULAR STUDENT SCRIPTS ──
function displayStudentInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    const preview = document.getElementById('studentPreview');
    if (!sel.value) { preview.style.display = 'none'; return; }
    document.getElementById('prevLrn').textContent = opt.dataset.lrn || 'None';
    document.getElementById('prevGender').textContent = opt.dataset.gender || '—';
    document.getElementById('prevEnroll').textContent = opt.dataset.lastEnroll || 'None';
    preview.style.display = 'block';
}

function updateRegGradeLevels() {
    const lt = document.getElementById('reg_level_type').value;
    const gl = document.getElementById('reg_grade_level');
    gl.innerHTML = '<option value="">-- Select Grade --</option>';
    const levels = lt === 'JHS' ? ['Grade 7','Grade 8','Grade 9','Grade 10'] : lt === 'SHS' ? ['Grade 11','Grade 12'] : [];
    levels.forEach(l => { const o = document.createElement('option'); o.value = o.textContent = l; gl.appendChild(o); });
    const isSHS = lt === 'SHS';
    ['regTrackDiv','regStrandDiv','regSemDiv'].forEach(id => document.getElementById(id).style.display = isSHS ? '' : 'none');
    document.getElementById('reg_section_id').innerHTML = '<option value="">-- Select Section (Optional) --</option>';
}

function updateRegStrands() {
    const track = document.getElementById('reg_track').value;
    const sel = document.getElementById('reg_strand');
    sel.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsTracks[track] || []).forEach(s => { const o = document.createElement('option'); o.value = o.textContent = s; sel.appendChild(o); });
    loadRegSections();
}

function loadRegSections() {
    const grade = document.getElementById('reg_grade_level').value;
    const sy    = document.getElementById('reg_school_year_id').value;
    const strand= document.getElementById('reg_strand') ? document.getElementById('reg_strand').value : '';
    const sel   = document.getElementById('reg_section_id');
    if (!grade) { sel.innerHTML = '<option value="">-- Select Grade First --</option>'; return; }
    sel.innerHTML = '<option value="">-- Loading... --</option>';
    fetch(`${apiUrl}?grade=${encodeURIComponent(grade)}&sy=${sy}&strand=${encodeURIComponent(strand)}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Select Section (Optional) --</option>';
            data.forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.section_name + (s.strand ? ` (${s.strand})` : ''); sel.appendChild(o); });
        });
}

// ── NEW / TRANSFEREE SCRIPTS ──
function toggleTransferFields() {
    const type = document.getElementById('new_enrollment_type').value;
    const card = document.getElementById('transferCard');
    const label = document.getElementById('schoolNameLabel');
    if (type === 'Transferee') {
        label.innerHTML = 'Last School Attended <span class="text-danger">*</span>';
        document.getElementById('last_school_attended').required = true;
        card.style.borderColor = '#2563eb';
    } else {
        label.innerHTML = 'Last School Attended';
        document.getElementById('last_school_attended').required = false;
        card.style.borderColor = '#e2e8f0';
    }
}

function updateNewGradeLevels() {
    const lt = document.getElementById('new_level_type').value;
    const gl = document.getElementById('new_grade_level');
    gl.innerHTML = '<option value="">-- Select Grade --</option>';
    const levels = lt === 'JHS' ? ['Grade 7','Grade 8','Grade 9','Grade 10'] : lt === 'SHS' ? ['Grade 11','Grade 12'] : [];
    levels.forEach(l => { const o = document.createElement('option'); o.value = o.textContent = l; gl.appendChild(o); });
    const isSHS = lt === 'SHS';
    ['newTrackDiv','newStrandDiv','newSemDiv'].forEach(id => document.getElementById(id).style.display = isSHS ? '' : 'none');
    document.getElementById('new_section_id').innerHTML = '<option value="">-- Select Section (Optional) --</option>';
}

function updateNewStrands() {
    const track = document.getElementById('new_track').value;
    const sel = document.getElementById('new_strand');
    sel.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsTracks[track] || []).forEach(s => { const o = document.createElement('option'); o.value = o.textContent = s; sel.appendChild(o); });
    loadNewSections();
}

function loadNewSections() {
    const grade = document.getElementById('new_grade_level').value;
    const sy    = document.getElementById('new_school_year_id').value;
    const strand= document.getElementById('new_strand') ? document.getElementById('new_strand').value : '';
    const sel   = document.getElementById('new_section_id');
    if (!grade) { sel.innerHTML = '<option value="">-- Select Grade First --</option>'; return; }
    sel.innerHTML = '<option value="">-- Loading... --</option>';
    fetch(`${apiUrl}?grade=${encodeURIComponent(grade)}&sy=${sy}&strand=${encodeURIComponent(strand)}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Select Section (Optional) --</option>';
            data.forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.section_name + (s.strand ? ` (${s.strand})` : ''); sel.appendChild(o); });
        });
}

document.getElementById('new_birthdate')?.addEventListener('change', function () {
    const age = Math.floor((new Date() - new Date(this.value)) / (365.25 * 24 * 3600 * 1000));
    document.getElementById('new_age_display').value = age > 0 ? age + ' yrs old' : '';
});

// Trigger initial on load if values exist
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('existing_student_id');
    if (sel && sel.value) displayStudentInfo(sel);
    toggleTransferFields();
});
</script>
@endpush
