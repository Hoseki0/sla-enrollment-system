@extends('layouts.admin')
@section('title', 'Enrollment Records')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-journal-check me-2 text-primary"></i>Enrollment Records</h2>
        <p>Manage and view all student enrollments.</p>
    </div>
    <a href="{{ route('admin.enrollments.create') }}" class="btn text-white fw-semibold px-3 rounded-pill"
       style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
        <i class="bi bi-person-plus me-1"></i>New Enrollment
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">School Year</label>
                <select name="sy" class="form-select form-select-sm">
                    @foreach($schoolYears as $s)
                    <option value="{{ $s->id }}" {{ $syId == $s->id ? 'selected' : '' }}>SY {{ $s->year_label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Level</label>
                <select name="level" class="form-select form-select-sm">
                    <option value="">All Levels</option>
                    <option value="JHS" {{ request('level')==='JHS'?'selected':'' }}>JHS</option>
                    <option value="SHS" {{ request('level')==='SHS'?'selected':'' }}>SHS</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Grade</label>
                <select name="grade" class="form-select form-select-sm">
                    <option value="">All Grades</option>
                    @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                    <option value="{{ $g }}" {{ request('grade')===$g?'selected':'' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['Pending','Enrolled','Cancelled','Dropped'] as $st)
                    <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Enrollment No.</th><th>Student Name</th><th>ID No.</th>
                        <th>Level</th><th>Grade</th><th>Strand</th><th>Section</th>
                        <th>Type</th><th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($enrollments as $e)
                <tr>
                    <td class="ps-3 fw-semibold" style="color:#1a3c6e;font-size:.82rem;">{{ $e->enrollment_no }}</td>
                    <td class="fw-semibold">{{ $e->student->full_name }}</td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $e->student->student_id }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $e->level_type==='JHS'?'badge-jhs':'badge-shs' }}">{{ $e->level_type }}</span></td>
                    <td style="font-size:.85rem;">{{ $e->grade_level }}</td>
                    <td style="font-size:.82rem;">{{ $e->strand ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->section?->section_name ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $e->enrollment_type }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $e->status_badge_class }}">{{ $e->status }}</span></td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $e->enrollment_date->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.enrollments.show', $e) }}"
                               class="btn btn-xs rounded-pill px-2 py-1" style="font-size:.75rem;background:#eff6ff;color:#1d4ed8;">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-xs rounded-pill px-2 py-1 update-status-btn"
                                    data-id="{{ $e->id }}" data-status="{{ $e->status }}"
                                    style="font-size:.75rem;background:#f0fdf4;color:#15803d;"
                                    data-bs-toggle="modal" data-bs-target="#statusModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center py-4 text-muted">No enrollment records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $enrollments->links() }}</div>
    </div>
</div>

{{-- Status Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="font-size:1rem;">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="statusForm">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <label class="form-label">New Status</label>
                    <select name="status" id="modal_status" class="form-select">
                        @foreach(['Pending','Enrolled','Cancelled','Dropped'] as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-3" style="background:#1a3c6e;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.update-status-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('statusForm').action = `/admin/enrollments/${id}/status`;
        document.getElementById('modal_status').value = this.dataset.status;
    });
});
</script>
@endpush
