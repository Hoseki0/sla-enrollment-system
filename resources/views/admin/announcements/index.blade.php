@extends('layouts.admin')
@section('title', 'Announcements')

@section('content')
<div class="section-header">
    <div>
        <h2>Announcements</h2>
        <p>Post and manage announcements for students.</p>
    </div>
    <a href="{{ route('admin.announcements.create') }}" class="btn text-white fw-semibold px-3"
       style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;">
        <i class="bi bi-megaphone me-1"></i> New Announcement
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Title</th>
                        <th>Audience</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Posted By</th>
                        <th>Expires</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($announcements as $ann)
                <tr>
                    <td class="ps-3 fw-semibold" style="font-size:.88rem;max-width:280px;">
                        <i class="bi {{ $ann->priority_icon }} me-1 text-{{ $ann->priority_badge }}"></i>
                        {{ $ann->title }}
                    </td>
                    <td>
                        <span class="badge rounded-pill px-2" style="background:#eff6ff;color:#1d4ed8;font-size:.72rem;">
                            {{ strtoupper($ann->audience) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $ann->priority_badge }} bg-opacity-10 text-{{ $ann->priority_badge }}" style="font-size:.72rem;">
                            {{ ucfirst($ann->priority) }}
                        </span>
                    </td>
                    <td>
                        @if($ann->is_published)
                            <span class="badge rounded-pill badge-enrolled">Published</span>
                        @else
                            <span class="badge rounded-pill badge-pending">Draft</span>
                        @endif
                    </td>
                    <td style="font-size:.82rem;">{{ $ann->admin->name ?? '—' }}</td>
                    <td style="font-size:.8rem;color:#64748b;">
                        {{ $ann->expires_at ? $ann->expires_at->format('M d, Y') : '—' }}
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.announcements.edit', $ann) }}" class="btn btn-sm btn-outline-primary me-1" style="font-size:.78rem;">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" class="d-inline"
                              onsubmit="return confirm('Delete this announcement?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" style="font-size:.78rem;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No announcements yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $announcements->links() }}
@endsection
