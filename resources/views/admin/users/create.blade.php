@extends('layouts.admin')
@section('title', 'Create Admin Account')

@section('content')
<div class="section-header">
    <div>
        <h2>Create Admin Account</h2>
        <p>Add a new administrator to the system.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select role...</option>
                            <option value="super_admin"  {{ old('role')=='super_admin' ?'selected':'' }}>Super Admin</option>
                            <option value="registrar"    {{ old('role')=='registrar'   ?'selected':'' }}>Registrar</option>
                            <option value="cashier"      {{ old('role')=='cashier'     ?'selected':'' }}>Cashier</option>
                            <option value="teacher"      {{ old('role')=='teacher'     ?'selected':'' }}>Teacher</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-semibold"
                            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;padding:.7rem;">
                        <i class="bi bi-person-check me-1"></i> Create Admin Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
