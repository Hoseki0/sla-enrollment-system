<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Parent Account | Sta. Lucia Academy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#003366; --primary-light:#00509e; --accent:#007bff; --accent-light:#e6f2ff; }
        body { font-family:'Inter',sans-serif; min-height:100vh; background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%); display:flex; align-items:center; justify-content:center; padding:1.5rem; }
        .reg-card { background:#fff; border-radius:24px; box-shadow:0 24px 64px rgba(0,20,50,.3); max-width:540px; width:100%; padding:2.25rem; }
        .btn-create { background:linear-gradient(135deg,var(--primary-light),var(--primary));border:none;color:#fff;font-weight:700;padding:.8rem;border-radius:10px;width:100%; }
    </style>
</head>
<body>
<div class="reg-card">
    <div class="text-center mb-3">
        <h1 class="h4 fw-bold mb-1" style="color:var(--primary);">Create Parent Account</h1>
        <p class="text-muted small mb-0">Register and connect to your child's student account to monitor attendance, grades, and payments.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('parent.register.post') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label fw-semibold small">Your Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Pedro Dela Cruz" required>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Relationship *</label>
                <select name="relationship" class="form-select" required>
                    <option value="Father" {{ old('relationship') === 'Father' ? 'selected' : '' }}>Father</option>
                    <option value="Mother" {{ old('relationship') === 'Mother' ? 'selected' : '' }}>Mother</option>
                    <option value="Guardian" {{ old('relationship') === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                </select>
            </div>
            <div class="col-md-7">
                <label class="form-label fw-semibold small">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="parent@email.com" required>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="0917...">
            </div>

            <div class="col-12">
                <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="fw-bold small mb-2" style="color:var(--primary);">
                        <i class="bi bi-link-45deg me-1"></i>Connect Child's Student Account
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Child's LRN *</label>
                            <input type="text" name="student_lrn" class="form-control" value="{{ old('student_lrn') }}" placeholder="e.g. 123456789012" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Child's Last Name *</label>
                            <input type="text" name="student_last_name" class="form-control" value="{{ old('student_last_name') }}" placeholder="e.g. Dela Cruz" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Password *</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>
        </div>

        <button type="submit" class="btn-create mt-4">
            <i class="bi bi-person-check me-2"></i>Create Account &amp; Connect Child
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('parent.login') }}" class="small fw-semibold text-decoration-none" style="color:var(--primary-light);">
            ← Back to Parent Login
        </a>
    </div>
</div>
</body>
</html>
