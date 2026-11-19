@extends('layouts.admin')
@section('title', 'Payments')

@section('content')
<div class="section-header">
    <div>
        <h2>Payment Records</h2>
        <p>All quarterly payment transactions — SY {{ $sy?->year_label ?? 'N/A' }}</p>
    </div>
    <a href="{{ route('admin.fees.index') }}" class="btn btn-primary rounded-3" style="background:var(--primary);border:none;">
        <i class="bi bi-gear me-1"></i> Fee Schedules
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-receipt text-white"></i></div>
            <div><div class="stat-val text-white">{{ $stats['total'] }}</div><div class="stat-label text-white-50">Total Records</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#15803d,#16a34a);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-check-circle text-white"></i></div>
            <div><div class="stat-val text-white">{{ $stats['paid'] }}</div><div class="stat-label text-white-50">Paid</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#a16207,#ca8a04);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-hourglass text-white"></i></div>
            <div><div class="stat-val text-white">{{ $stats['pending'] }}</div><div class="stat-label text-white-50">Pending</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-cash-stack text-white"></i></div>
            <div><div class="stat-val text-white" style="font-size:1.3rem;">₱{{ number_format($stats['collected'], 0) }}</div><div class="stat-label text-white-50">Collected</div></div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['paid','pending','failed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quarter</label>
                <select name="quarter" class="form-select">
                    <option value="">All</option>
                    @for($q=1;$q<=4;$q++)
                    <option value="{{ $q }}" {{ request('quarter')==$q?'selected':'' }}>Q{{ $q }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Grade Level</label>
                <select name="grade_level" class="form-select">
                    <option value="">All</option>
                    @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
                    <option value="{{ $gl }}" {{ request('grade_level')===$gl?'selected':'' }}>{{ $gl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-3" style="background:var(--primary);border:none;">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Reference</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Quarter</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="ps-3" style="font-size:.8rem;font-weight:600;">{{ $p->payment_ref }}</td>
                    <td>
                        <div style="font-size:.85rem;font-weight:600;">{{ $p->student->full_name }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $p->student->student_id }}</div>
                    </td>
                    <td style="font-size:.82rem;">{{ $p->enrollment->grade_level }}</td>
                    <td>Q{{ $p->quarter }}</td>
                    <td>₱{{ number_format($p->amount, 2) }}</td>
                    <td>{{ $p->payment_method_label }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $p->status_badge_class }}">{{ ucfirst($p->status) }}</span></td>
                    <td style="font-size:.82rem;">{{ $p->paid_at?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        @if($p->status !== 'paid')
                        <form method="POST" action="{{ route('admin.payments.markPaid', $p) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-3" onclick="return confirm('Mark as paid?')">Mark Paid</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No payment records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
