<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment | Sta. Lucia Academy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#1a3c6e; --accent:#e8a020; --accent-light:#f5c04a; --green:#15803d; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#0d2b50 0%,#1a3c6e 60%,#15803d 100%); min-height:100vh; padding:2rem 0; }
        .page-wrapper { max-width:860px; margin:0 auto; padding:0 1rem; }
        .page-header { text-align:center; margin-bottom:2rem; }
        .page-header .logo { width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-light));display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto .75rem;box-shadow:0 8px 25px rgba(232,160,32,.4);overflow:hidden; }
        .page-header .logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .page-header h1 { color:#fff;font-size:1.6rem;font-weight:800;margin:.5rem 0 .25rem; }
        .page-header p  { color:rgba(255,255,255,.55);font-size:.85rem; }
        .card { border:1px solid rgba(255,255,255,.1);border-radius:18px;background:rgba(255,255,255,.06);backdrop-filter:blur(16px);margin-bottom:1.25rem; }
        .card-header { background:rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.08);border-radius:18px 18px 0 0;padding:1rem 1.25rem;color:#fff;font-weight:700;font-size:.92rem; }
        .card-header.primary { background:linear-gradient(135deg,#1a3c6e,#2356a0); }
        .card-body { padding:1.25rem; }
        .form-label { color:rgba(255,255,255,.65);font-size:.8rem;font-weight:500;margin-bottom:.35rem; }
        .form-control, .form-select { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:10px;font-size:.88rem; }
        .form-control:focus, .form-select:focus { background:rgba(255,255,255,.1);border-color:var(--accent);box-shadow:0 0 0 3px rgba(232,160,32,.15);color:#fff; }
        .form-control::placeholder { color:rgba(255,255,255,.25); }
        .form-select option { background:#1a3c6e;color:#fff; }
        .text-required { color:#f87171; }
        .section-label { color:rgba(255,255,255,.35);font-size:.68rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin:0 0 .6rem;padding-bottom:.4rem;border-bottom:1px solid rgba(255,255,255,.07); }
        .btn-submit { background:linear-gradient(135deg,var(--green),#16a34a);border:none;color:#fff;font-weight:700;padding:.75rem 2rem;border-radius:12px;font-size:.95rem;transition:all .3s;box-shadow:0 6px 20px rgba(21,128,61,.4); }
        .btn-submit:hover { transform:translateY(-2px);box-shadow:0 10px 28px rgba(21,128,61,.55); }
        .back-link { color:rgba(255,255,255,.45);font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;margin-bottom:1.25rem; }
        .back-link:hover { color:rgba(255,255,255,.75); }
        .alert-error { background:rgba(220,53,69,.15);border:1px solid rgba(220,53,69,.3);color:#ff6b7a;border-radius:12px;font-size:.85rem;padding:.8rem 1rem;margin-bottom:1rem; }
        .password-note { background:rgba(232,160,32,.1);border:1px solid rgba(232,160,32,.2);border-radius:10px;padding:.65rem 1rem;color:rgba(255,255,255,.6);font-size:.78rem;margin-top:.35rem; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <a href="{{ route('student.login') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Login</a>

    <div class="page-header">
        <div class="logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="SLA">
            @elseif(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="SLA">
            @else 🏫 @endif
        </div>
        <h1>Student Enrollment Form</h1>
        <p>Sta. Lucia Academy — SY {{ $sy?->year_label ?? date('Y').'-'.(date('Y')+1) }}</p>
    </div>

    @if($errors->any())
    <div class="alert-error"><i class="bi bi-exclamation-triangle me-2"></i><strong>Please fix the following:</strong>
        <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('student.enroll.post') }}" novalidate>
        @csrf

        {{-- Enrollment Info --}}
        <div class="card">
            <div class="card-header primary"><i class="bi bi-journal-check me-2"></i>Enrollment Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">School Year <span class="text-required">*</span></label>
                        <select name="school_year_id" class="form-select" required>
                            @foreach($schoolYears as $s)
                            <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>SY {{ $s->year_label }}{{ $s->is_active ? ' (Active)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Enrollment Type <span class="text-required">*</span></label>
                        <select name="enrollment_type" class="form-select" required>
                            @foreach(['New','Old','Transferee','Returnee'] as $t)
                            <option value="{{ $t }}" {{ old('enrollment_type')===$t?'selected':'' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Level Type <span class="text-required">*</span></label>
                        <select name="level_type" id="level_type" class="form-select" required onchange="updateGrades()">
                            <option value="">-- Select --</option>
                            <option value="JHS" {{ old('level_type')==='JHS'?'selected':'' }}>Junior High School (JHS)</option>
                            <option value="SHS" {{ old('level_type')==='SHS'?'selected':'' }}>Senior High School (SHS)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade Level <span class="text-required">*</span></label>
                        <select name="grade_level" id="grade_level" class="form-select" required onchange="loadSections()">
                            <option value="">-- Select Grade --</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="trackDiv" style="display:none;">
                        <label class="form-label">Track</label>
                        <select name="track" id="track" class="form-select" onchange="updateStrands()">
                            <option value="">-- Select Track --</option>
                            @foreach(array_keys($shsTracks) as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3" id="strandDiv" style="display:none;">
                        <label class="form-label">Strand</label>
                        <select name="strand" id="strand" class="form-select" onchange="loadSections()">
                            <option value="">-- Select Strand --</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="semDiv" style="display:none;">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preferred Section</label>
                        <select name="section_id" id="section_id" class="form-select">
                            <option value="">-- Optional --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Info --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Personal Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Last Name <span class="text-required">*</span></label><input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}" placeholder="Dela Cruz"></div>
                    <div class="col-md-3"><label class="form-label">First Name <span class="text-required">*</span></label><input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}" placeholder="Juan"></div>
                    <div class="col-md-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}"></div>
                    <div class="col-md-1"><label class="form-label">Suffix</label><input type="text" name="suffix" class="form-control" value="{{ old('suffix') }}" maxlength="10"></div>
                    <div class="col-md-2"><label class="form-label">LRN</label><input type="text" name="lrn" class="form-control" maxlength="12" value="{{ old('lrn') }}" placeholder="12-digit"></div>
                    <div class="col-md-3"><label class="form-label">Date of Birth <span class="text-required">*</span></label><input type="date" name="birthdate" id="birthdate" class="form-control" required value="{{ old('birthdate') }}"></div>
                    <div class="col-md-2"><label class="form-label">Age</label><input type="text" id="age_display" class="form-control" readonly placeholder="Auto"></div>
                    <div class="col-md-2"><label class="form-label">Gender <span class="text-required">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">--</option>
                            <option value="Male" {{ old('gender')==='Male'?'selected':'' }}>Male</option>
                            <option value="Female" {{ old('gender')==='Female'?'selected':'' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-2"><label class="form-label">Civil Status</label>
                        <select name="civil_status" class="form-select">
                            @foreach(['Single','Married','Widowed'] as $cs)
                            <option value="{{ $cs }}" {{ old('civil_status','Single')===$cs?'selected':'' }}>{{ $cs }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Religion</label><input type="text" name="religion" class="form-control" value="{{ old('religion') }}" placeholder="Roman Catholic"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="student@email.com"></div>
                    <div class="col-md-3"><label class="form-label">Contact Number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX"></div>
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-geo-alt me-2"></i>Address</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Street / House No. <span class="text-required">*</span></label><input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="123 Mabini St."></div>
                    <div class="col-md-3"><label class="form-label">Barangay</label><input type="text" name="barangay" class="form-control" value="{{ old('barangay') }}"></div>
                    <div class="col-md-3"><label class="form-label">Municipality / City</label><input type="text" name="municipality" class="form-control" value="{{ old('municipality') }}"></div>
                    <div class="col-md-3"><label class="form-label">Province</label><input type="text" name="province" class="form-control" value="{{ old('province') }}"></div>
                    <div class="col-md-2"><label class="form-label">ZIP Code</label><input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" maxlength="10"></div>
                </div>
            </div>
        </div>

        {{-- Parents --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-house-heart me-2"></i>Parents / Guardian</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12"><p class="section-label">Father</p></div>
                    <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}"></div>
                    <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation') }}"></div>
                    <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="father_contact" class="form-control" value="{{ old('father_contact') }}"></div>
                    <div class="col-12"><p class="section-label">Mother</p></div>
                    <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}"></div>
                    <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation') }}"></div>
                    <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="mother_contact" class="form-control" value="{{ old('mother_contact') }}"></div>
                    <div class="col-12"><p class="section-label">Guardian (if different)</p></div>
                    <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}"></div>
                    <div class="col-md-3"><label class="form-label">Relationship</label><input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship') }}"></div>
                    <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact') }}"></div>
                </div>
            </div>
        </div>

        {{-- Previous School --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-building me-2"></i>Previous School Attended</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5"><label class="form-label">School Name</label><input type="text" name="last_school_attended" class="form-control" value="{{ old('last_school_attended') }}"></div>
                    <div class="col-md-5"><label class="form-label">School Address</label><input type="text" name="last_school_address" class="form-control" value="{{ old('last_school_address') }}"></div>
                    <div class="col-md-2"><label class="form-label">Type</label>
                        <select name="school_type" class="form-select">
                            <option value="Public" {{ old('school_type','Public')==='Public'?'selected':'' }}>Public</option>
                            <option value="Private" {{ old('school_type')==='Private'?'selected':'' }}>Private</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Portal Password --}}
        <div class="card">
            <div class="card-header" style="background:linear-gradient(135deg,#15803d,#16a34a);border-radius:18px 18px 0 0;">
                <i class="bi bi-shield-lock me-2"></i>Create Your Portal Password
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Password <span class="text-required">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="At least 6 characters">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Confirm Password <span class="text-required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat your password">
                    </div>
                </div>
                <div class="password-note mt-2"><i class="bi bi-info-circle me-1"></i>You will use this password to log in to the Student Portal after your enrollment is approved.</div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pb-4">
            <a href="{{ route('student.login') }}" class="btn btn-outline-light rounded-pill px-4" style="opacity:.6;">Cancel</a>
            <button type="submit" id="submitBtn" class="btn-submit rounded-pill">
                <i class="bi bi-send me-2"></i>Submit Enrollment Application
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const shsTracks = @json($shsTracks);
const apiUrl    = "{{ route('student.enroll.sections') }}";
const syId      = document.querySelector('[name=school_year_id]');

function updateGrades() {
    const lt = document.getElementById('level_type').value;
    const gl = document.getElementById('grade_level');
    gl.innerHTML = '<option value="">-- Select Grade --</option>';
    const levels = lt === 'JHS' ? ['Grade 7','Grade 8','Grade 9','Grade 10'] : lt === 'SHS' ? ['Grade 11','Grade 12'] : [];
    levels.forEach(l => { const o = document.createElement('option'); o.value = o.textContent = l; gl.appendChild(o); });
    ['trackDiv','strandDiv','semDiv'].forEach(id => document.getElementById(id).style.display = lt === 'SHS' ? '' : 'none');
    document.getElementById('section_id').innerHTML = '<option value="">-- Optional --</option>';
}

function updateStrands() {
    const t = document.getElementById('track').value;
    const s = document.getElementById('strand');
    s.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsTracks[t] || []).forEach(st => { const o = document.createElement('option'); o.value = o.textContent = st; s.appendChild(o); });
    loadSections();
}

function loadSections() {
    const grade  = document.getElementById('grade_level').value;
    const strand = document.getElementById('strand') ? document.getElementById('strand').value : '';
    const sy     = document.querySelector('[name=school_year_id]').value;
    const sel    = document.getElementById('section_id');
    if (!grade) return;
    sel.innerHTML = '<option value="">Loading...</option>';
    fetch(`${apiUrl}?grade=${encodeURIComponent(grade)}&sy=${sy}&strand=${encodeURIComponent(strand)}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Optional --</option>';
            data.forEach(s => { const o = document.createElement('option'); o.value = s.id; o.textContent = s.section_name + (s.strand ? ` (${s.strand})` : ''); sel.appendChild(o); });
        });
}

document.getElementById('birthdate').addEventListener('change', function () {
    const age = Math.floor((new Date() - new Date(this.value)) / (365.25 * 24 * 3600 * 1000));
    document.getElementById('age_display').value = age > 0 ? age + ' yrs old' : '';
});

document.getElementById('strand').addEventListener('change', loadSections);

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    btn.disabled = true;
});
</script>
</body>
</html>
