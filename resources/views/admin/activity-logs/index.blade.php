@extends('layouts.admin')
@section('title', 'Activity Log')

@section('content')
<div class="section-header">
    <div>
        <h2>Activity Log</h2>
        <p>Audit trail of all system actions.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3" style="width:170px;">Date & Time</th>
                        <th>Actor</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-3" style="font-size:.8rem;color:#64748b;white-space:nowrap;">
                        {{ $log->created_at->format('M d, Y H:i:s') }}
                    </td>
                    <td class="fw-semibold" style="font-size:.87rem;">{{ $log->actor_name }}</td>
                    <td>
                        <span class="badge rounded-pill px-2"
                              style="font-size:.7rem;background:{{ $log->actor_type==='admin' ? '#fef3c7' : '#eff6ff' }};
                                     color:{{ $log->actor_type==='admin' ? '#92400e' : '#1d4ed8' }};">
                            {{ ucfirst($log->actor_type) }}
                        </span>
                    </td>
                    <td>
                        <code style="font-size:.75rem;background:#f1f5f9;padding:.15rem .4rem;border-radius:4px;color:#475569;">
                            <i class="bi {{ $log->action_icon }} me-1"></i>{{ $log->action }}
                        </code>
                    </td>
                    <td style="font-size:.85rem;max-width:320px;">{{ $log->description }}</td>
                    <td style="font-size:.8rem;color:#94a3b8;">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">No activity recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $logs->links() }}
@endsection
