@extends('layouts.parent')
@section('title', 'Child Payments')

@section('content')
@if(!$child)
    <div class="alert alert-warning">Please connect a child account first.</div>
@else
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Tuition &amp; Payments — {{ $child->full_name }}</h4>
        <div class="text-muted small">LRN: {{ $child->lrn }} &bull; SY {{ $sy?->year_label ?? 'N/A' }}</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <div class="text-muted small">Total Paid</div>
                <div class="h4 fw-bold text-success mb-0">₱{{ number_format($totalPaid, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <div class="text-muted small">Remaining Balance</div>
                <div class="h4 fw-bold text-danger mb-0">₱{{ number_format($totalBalance, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Quarterly Payment Schedule</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Quarter</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td class="ps-3 fw-semibold">Quarter {{ $p->quarter }}</td>
                            <td class="small text-muted">{{ $p->payment_ref }}</td>
                            <td class="fw-semibold">₱{{ number_format($p->amount, 2) }}</td>
                            <td class="text-uppercase small">{{ $p->payment_method ?? '—' }}</td>
                            <td>
                                @if($p->status === 'paid')
                                    <span class="badge bg-success-subtle text-success rounded-pill">Paid</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Pending</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $p->paid_at ? $p->paid_at->format('M d, Y') : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No payment records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
