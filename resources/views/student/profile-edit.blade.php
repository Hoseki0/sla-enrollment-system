@extends('layouts.student')
@section('title', 'Edit Profile')

@section('content')
<div class="section-header">
    <div>
        <h2>Edit Profile</h2>
        <p>Update your contact information and account password.</p>
    </div>
    <a href="{{ route('student.profile') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">
    {{-- Contact Information --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Contact & Address</div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.profile.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $student->email) }}" placeholder="your@email.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control"
                                   value="{{ old('contact_number', $student->contact_number) }}" placeholder="09XXXXXXXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Complete Address</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address', $student->address) }}">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" class="form-control" value="{{ old('barangay', $student->barangay) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Municipality/City</label>
                            <input type="text" name="municipality" class="form-control" value="{{ old('municipality', $student->municipality) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Province</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $student->province) }}">
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-bold mb-3" style="color:#1a3c6e;">Guardian Information</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship', $student->guardian_relationship) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No.</label>
                            <input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact', $student->guardian_contact) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 fw-semibold text-white"
                            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;padding:.7rem;">
                        <i class="bi bi-save me-1"></i> Save Contact Info
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-2" style="color:#7c3aed;"></i>Change Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.profile.password') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold text-white"
                            style="background:linear-gradient(135deg,#6d28d9,#7c3aed);border-radius:10px;padding:.7rem;">
                        <i class="bi bi-key me-1"></i> Change Password
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Note</div>
            <div class="card-body">
                <ul class="mb-0" style="font-size:.83rem;color:#64748b;padding-left:1.2rem;">
                    <li>Name, LRN, birthday, and gender can only be updated by the Registrar.</li>
                    <li>Contact admin if you need to update your personal records.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
