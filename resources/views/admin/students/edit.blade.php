@extends('layouts.admin')
@section('title', 'Edit Student')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-pencil me-2 text-primary"></i>Edit Student</h2>
        <p>{{ $student->full_name }} &mdash; LRN: {{ $student->lrn ?? '—' }}</p>
    </div>
    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($errors->any())
<div class="alert border-0 rounded-3 mb-4" style="background:#fee2e2;color:#dc2626;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1 ps-4">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.students.update', $student) }}">
    @csrf @method('PUT')

    {{-- Personal Info --}}
    <div class="card mb-4">
        <div class="card-header" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);color:#fff;border-radius:15px 15px 0 0;">
            <i class="bi bi-person me-2"></i>Personal Information
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Last Name <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" required value="{{ old('last_name', $student->last_name) }}"></div>
                <div class="col-md-3"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $student->first_name) }}"></div>
                <div class="col-md-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $student->middle_name) }}"></div>
                <div class="col-md-1"><label class="form-label">Suffix</label><input type="text" name="suffix" class="form-control" value="{{ old('suffix', $student->suffix) }}" maxlength="10"></div>
                <div class="col-md-2"><label class="form-label">LRN</label><input type="text" name="lrn" class="form-control" maxlength="12" value="{{ old('lrn', $student->lrn) }}"></div>
                <div class="col-md-3"><label class="form-label">Date of Birth <span class="text-danger">*</span></label><input type="date" name="birthdate" class="form-control" required value="{{ old('birthdate', $student->birthdate?->format('Y-m-d')) }}"></div>
                <div class="col-md-2"><label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" {{ old('gender', $student->gender)==='Male'?'selected':'' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender)==='Female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label">Civil Status</label>
                    <select name="civil_status" class="form-select">
                        @foreach(['Single','Married','Widowed'] as $cs)
                        <option value="{{ $cs }}" {{ old('civil_status', $student->civil_status)===$cs?'selected':'' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Religion</label><input type="text" name="religion" class="form-control" value="{{ old('religion', $student->religion) }}"></div>
                <div class="col-md-3"><label class="form-label">Nationality</label><input type="text" name="nationality" class="form-control" value="{{ old('nationality', $student->nationality) }}"></div>
                <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}"></div>
                <div class="col-md-3"><label class="form-label">Contact Number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $student->contact_number) }}"></div>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;"><i class="bi bi-geo-alt me-2 text-primary"></i>Address</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Street / House No. <span class="text-danger">*</span></label><input type="text" name="address" class="form-control" required value="{{ old('address', $student->address) }}"></div>
                <div class="col-md-3"><label class="form-label">Barangay</label><input type="text" name="barangay" class="form-control" value="{{ old('barangay', $student->barangay) }}"></div>
                <div class="col-md-3"><label class="form-label">Municipality / City</label><input type="text" name="municipality" class="form-control" value="{{ old('municipality', $student->municipality) }}"></div>
                <div class="col-md-3"><label class="form-label">Province</label><input type="text" name="province" class="form-control" value="{{ old('province', $student->province) }}"></div>
                <div class="col-md-2"><label class="form-label">ZIP Code</label><input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $student->zip_code) }}" maxlength="10"></div>
            </div>
        </div>
    </div>

    {{-- Parents --}}
    <div class="card mb-4">
        <div class="card-header" style="background:#f8faff;"><i class="bi bi-house-heart me-2 text-primary"></i>Parents / Guardian</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">FATHER</p></div>
                <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}"></div>
                <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation', $student->father_occupation) }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="father_contact" class="form-control" value="{{ old('father_contact', $student->father_contact) }}"></div>
                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">MOTHER</p></div>
                <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name) }}"></div>
                <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $student->mother_occupation) }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="mother_contact" class="form-control" value="{{ old('mother_contact', $student->mother_contact) }}"></div>
                <div class="col-12"><p class="text-muted fw-semibold mb-1" style="font-size:.8rem;">GUARDIAN</p></div>
                <div class="col-md-4"><label class="form-label">Name</label><input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}"></div>
                <div class="col-md-3"><label class="form-label">Relationship</label><input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship', $student->guardian_relationship) }}"></div>
                <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact', $student->guardian_contact) }}"></div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary px-4 rounded-pill">Cancel</a>
        <button type="submit" class="btn text-white px-5 rounded-pill fw-semibold" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <i class="bi bi-check2 me-2"></i>Save Changes
        </button>
    </div>
</form>
@endsection
