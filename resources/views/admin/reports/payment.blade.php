@extends('layouts.admin')
@section('title', 'Payment Collection Report')

@section('content')
<div class="section-header">
    <div>
        <h2>Payment Collection Report</h2>
        <p>Summary of tuition fee collections.</p>
    </div>
    <button onclick="window.print()" class="btn text-white fw-semibold px-3"
            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;">
        <i class="bi bi-printer me-1"></i> Print / Save PDF
    </button>
</div>

{{-- Filters --}}
<form class="row g-2 mb-4" method="GET">
    <div class="col-auto">
        <select name="sy" class="form-select form-select-sm">
            @foreach($schoolYears as $year)
            <option value="{{ $year->id }}" {{ $syId == $year->id ? 'selected' : '' }}>SY {{ $year->year_label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="quarter" class="form-select form-select-sm">
            <option value="">All Quarters</option>
            @for ($q = 1; $q <= 4; $q++)
            <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>Quarter {{ $q }}</option>
            @endfor
        </select>
    </div>
    <div class="col-auto">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Paid</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('admin.reports.payments') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
    </div>
</form>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#15803d,#16a34a);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-cash-coin text-white"></i></div>
            <div>
                <div class="stat-val text-white">₱{{ number_format($summary['total_collected'], 2) }}</div>
                <div class="stat-label">Total Collected</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#a16207,#ca8a04);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-hourglass text-white"></i></div>
            <div>
                <div class="stat-val text-white">₱{{ number_format($summary['total_pending'], 2) }}</div>
                <div class="stat-label">Total Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-receipt text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ $summary['count_paid'] }}</div>
                <div class="stat-label">Paid Transactions</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-clock text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ $summary['count_pending'] }}</div>
                <div class="stat-label">Pending Transactions</div>
            </div>
        </div>
    </div>
</div>

{{-- Print header --}}
<div class="d-none d-print-block mb-4 text-center">
    <h4 class="fw-bold mb-0" style="color:#1a3c6e;">{{ \App\Models\Setting::get('school_name', 'Sta. Lucia Academy') }}</h4>
    <h5 class="mt-2 fw-bold">PAYMENT COLLECTION REPORT — SY {{ $selectedSy?->year_label }}</h5>
    <div style="font-size:.85rem;">Printed: {{ now()->format('F d, Y h:i A') }}</div>
    <hr>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Payment Ref.</th>
                        <th>Student</th>
                        <th>Quarter</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $i => $p)
                <tr>
                    <td class="ps-3 text-muted" style="font-size:.82rem;">{{ $i + 1 }}</td>
                    <td class="fw-semibold text-primary" style="font-size:.82rem;">{{ $p->payment_ref }}</td>
                    <td style="font-size:.87rem;">{{ $p->student->full_name }}</td>
                    <td style="font-size:.85rem;">Q{{ $p->quarter }}</td>
                    <td class="fw-semibold" style="font-size:.88rem;">₱{{ number_format($p->amount, 2) }}</td>
                    <td style="font-size:.82rem;">{{ $p->payment_method_label }}</td>
                    <td>
                        <span class="badge rounded-pill px-2 {{ $p->status_badge_class }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td style="font-size:.8rem;color:#64748b;">
                        {{ $p->paid_at ? $p->paid_at->format('M d, Y') : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .section-header .btn, form, .alert, .row.g-3 { display: none !important; }
    .main-content { margin: 0 !important; padding: 1rem !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
