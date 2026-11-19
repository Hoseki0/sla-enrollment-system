@extends('layouts.admin')
@section('title', 'Admin Users')

@section('content')
<div class="section-header">
    <div>
        <h2>Admin Users</h2>
        <p>Manage administrator accounts and permissions.</p>
    </div>
    @if(auth('admin')->user()->isSuperAdmin())
    <a href="{{ route('admin.users.create') }}" class="btn text-white fw-semibold px-3"
       style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;">
        <i class="bi bi-person-plus me-1"></i> Add Admin
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 datatable">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1a3c6e,#2356a0);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem;">{{ $admin->name }}</div>
                                @if($admin->id === auth('admin')->id())
                                <span style="font-size:.7rem;color:#1d4ed8;">(You)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.87rem;">{{ $admin->email }}</td>
                    <td>
                        <span class="badge rounded-pill px-2"
                            style="background:{{ $admin->role === 'super_admin' ? '#fef3c7' : '#eff6ff' }};
                                   color:{{ $admin->role === 'super_admin' ? '#92400e' : '#1d4ed8' }};font-size:.75rem;">
                            {{ $admin->role_label }}
                        </span>
                    </td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $admin->created_at->format('M d, Y') }}</td>
                    <td class="text-end pe-3">
                        @if(auth('admin')->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.edit', $admin) }}" class="btn btn-sm btn-outline-primary me-1" style="font-size:.78rem;">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @if($admin->id !== auth('admin')->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" class="d-inline"
                              onsubmit="return confirm('Delete this admin account?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" style="font-size:.78rem;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
