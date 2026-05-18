@extends('layouts.admin')
@section('title', 'School Years')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-calendar3 me-2 text-primary"></i>School Years</h2>
        <p>Manage academic school years and set the active year.</p>
    </div>
    <button class="btn text-white fw-semibold px-3 rounded-pill"
            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);"
            data-bs-toggle="modal" data-bs-target="#addSyModal">
        <i class="bi bi-plus-lg me-1"></i>Add School Year
    </button>
</div>

<div class="row g-3">
@forelse($schoolYears as $sy)
<div class="col-md-4">
    <div class="card h-100" style="border-radius:16px;border:{{ $sy->is_active ? '2px solid #1a3c6e' : '1px solid #e2e8f0' }};">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-bold" style="font-size:1.1rem;">SY {{ $sy->year_label }}</div>
                    <div style="font-size:.78rem;color:#64748b;">Academic Year</div>
                </div>
                @if($sy->is_active)
                <span class="badge rounded-pill px-3 py-2" style="background:#dcfce7;color:#15803d;font-size:.75rem;">
                    <i class="bi bi-check-circle me-1"></i>Active
                </span>
                @else
                <span class="badge rounded-pill px-3 py-2" style="background:#f1f5f9;color:#64748b;font-size:.75rem;">Inactive</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <div style="width:40px;height:40px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:1rem;">
                    <i class="bi bi-journal-check" style="color:#1d4ed8;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1rem;">{{ number_format($sy->enrollments_count) }}</div>
                    <div style="font-size:.75rem;color:#64748b;">Total Enrollments</div>
                </div>
            </div>
            @if(!$sy->is_active)
            <form method="POST" action="{{ route('admin.school-years.activate', $sy) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm w-100 rounded-pill text-white"
                        style="background:#1a3c6e;font-size:.82rem;"
                        onclick="return confirm('Set SY {{ $sy->year_label }} as active?')">
                    <i class="bi bi-check2-circle me-1"></i>Set as Active
                </button>
            </form>
            @else
            <div class="text-center" style="font-size:.78rem;color:#64748b;padding:.4rem;">
                <i class="bi bi-star-fill text-warning me-1"></i>Current active school year
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="col-12"><div class="card text-center py-5"><div class="text-muted">No school years found.</div></div></div>
@endforelse
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addSyModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold">Add School Year</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.school-years.store') }}">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert py-2 mb-2" style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;font-size:.85rem;">
                        {{ $errors->first() }}
                    </div>
                    @endif
                    <label class="form-label">Year Label</label>
                    <input type="text" name="year_label" class="form-control"
                           placeholder="e.g. 2026-2027" pattern="\d{4}-\d{4}" required value="{{ old('year_label') }}">
                    <small class="text-muted" style="font-size:.75rem;">Format: YYYY-YYYY</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4" style="background:#1a3c6e;">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
