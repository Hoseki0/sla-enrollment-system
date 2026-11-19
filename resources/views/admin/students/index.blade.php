@extends('layouts.admin')
@section('title', 'Students')

@section('content')
<div class="section-header">
    <div><h2><i class="bi bi-people me-2 text-primary"></i>Student Records</h2><p>View and manage all registered students.</p></div>
    <a href="{{ route('admin.enrollments.create') }}" class="btn text-white fw-semibold px-3 rounded-pill" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
        <i class="bi bi-person-plus me-1"></i>New Enrollment
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">LRN</th><th>Full Name</th>
                        <th>Gender</th><th>Birthdate</th><th>Contact</th><th>Enrollments</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($students as $s)
                <tr>
                    <td class="ps-3 fw-semibold text-primary" style="font-size:.82rem;">{{ $s->lrn ?? '—' }}</td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $s->student_id }}</td>
                    <td class="fw-semibold">{{ $s->full_name }}</td>
                    <td>
                        <span class="badge rounded-pill px-2" style="background:{{ $s->gender==='Male'?'#eff6ff;color:#1d4ed8':'#fdf4ff;color:#7e22ce' }}">{{ $s->gender }}</span>
                    </td>
                    <td style="font-size:.82rem;">{{ $s->birthdate?->format('M d, Y') ?? '—' }}</td>
                    <td style="font-size:.82rem;">{{ $s->contact_number ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-3" style="background:#f0fdf4;color:#15803d;font-size:.78rem;">{{ $s->enrollments_count }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.students.show', $s) }}" class="btn btn-xs rounded-pill px-2 py-1" style="font-size:.75rem;background:#eff6ff;color:#1d4ed8;" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.students.edit', $s) }}" class="btn btn-xs rounded-pill px-2 py-1" style="font-size:.75rem;background:#fefce8;color:#a16207;" title="Edit"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No students found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $students->links() }}</div>
    </div>
</div>
@endsection
