@extends('layouts.student')
@section('title', 'Quarterly Payments')

@push('styles')
<style>
    .quarter-card { border-radius:16px;border:2px solid var(--border);padding:1.5rem;transition:all .2s;height:100%; }
    .quarter-card.paid { border-color:#16a34a;background:#f0fdf4; }
    .quarter-card.pending { border-color:#ca8a04;background:#fefce8; }
    .quarter-card.overdue { border-color:#dc2626;background:#fef2f2; }
    .pay-method-btn { border:2px solid var(--border);border-radius:12px;padding:1rem;cursor:pointer;transition:all .2s;text-align:center;background:#fff; }
    .pay-method-btn:hover { border-color:var(--primary-light);background:var(--accent-light); }
    .pay-method-btn.selected { border-color:var(--primary);background:var(--accent-light); }
    .pay-method-btn img { height:32px;margin-bottom:.5rem; }
    .gcash-color { color:#007dfe; }
    .maya-color { color:#00d632; }
</style>
@endpush

@section('content')
<div class="section-header">
    <div>
        <h2>Quarterly Payments</h2>
        <p class="text-muted mb-0">Pay your tuition fees before quarterly examinations — SY {{ $sy?->year_label ?? 'N/A' }}</p>
    </div>
</div>

@if(!$enrollment)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-credit-card-2-front fs-1 text-muted opacity-25 d-block mb-3"></i>
        <h5>No Active Enrollment</h5>
        <p class="text-muted">You need an enrolled status for the current school year to view and make payments.</p>
    </div>
</div>
@else
{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a3c6e,#2356a0);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-wallet2 text-white"></i></div>
            <div>
                <div class="stat-val text-white">₱{{ number_format($summary['total_due'], 2) }}</div>
                <div class="stat-label">Total Due</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#15803d,#16a34a);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-check-circle text-white"></i></div>
            <div>
                <div class="stat-val text-white">₱{{ number_format($summary['total_paid'], 2) }}</div>
                <div class="stat-label">Total Paid</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-exclamation-circle text-white"></i></div>
            <div>
                <div class="stat-val text-white">₱{{ number_format($summary['balance'], 2) }}</div>
                <div class="stat-label">Balance</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);"><i class="bi bi-calendar-check text-white"></i></div>
            <div>
                <div class="stat-val text-white">{{ $summary['paid_count'] }}/4</div>
                <div class="stat-label">Quarters Paid</div>
            </div>
        </div>
    </div>
</div>

<div class="alert border-0 rounded-3 mb-4" style="background:#eff6ff;color:#1d4ed8;">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Payment required before exams.</strong> You must pay the corresponding quarter fee before taking quarterly examinations.
    {{ $enrollment->grade_level }} @if($enrollment->strand)· {{ $enrollment->strand }}@endif
</div>

{{-- Quarter Cards --}}
<div class="row g-3">
@foreach($summary['payments'] as $payment)
@php
    $isPaid = $payment->status === 'paid';
    $isOverdue = !$isPaid && $payment->feeSchedule?->due_date && $payment->feeSchedule->due_date->isPast();
    $cardClass = $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'pending');
@endphp
<div class="col-md-6 col-lg-3">
    <div class="quarter-card {{ $cardClass }}">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Quarter {{ $payment->quarter }}</div>
                <div style="font-size:1.5rem;font-weight:800;color:var(--primary);">₱{{ number_format($payment->amount, 2) }}</div>
            </div>
            <span class="badge rounded-pill px-2 {{ $payment->status_badge_class }}">{{ ucfirst($payment->status) }}</span>
        </div>

        @if($payment->feeSchedule?->due_date)
        <div class="text-muted mb-2" style="font-size:.8rem;">
            <i class="bi bi-calendar3 me-1"></i> Due: {{ $payment->feeSchedule->due_date->format('M d, Y') }}
        </div>
        @endif

        @if($isPaid)
        <div class="text-success" style="font-size:.82rem;">
            <i class="bi bi-check-circle-fill me-1"></i>
            Paid via {{ $payment->payment_method_label }}
            <br><small class="text-muted">{{ $payment->paid_at?->format('M d, Y h:i A') }}</small>
        </div>
        @else
        <form method="POST" action="{{ route('student.payments.pay', $payment->quarter) }}" class="payment-form">
            @csrf
            <div class="mb-2" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Pay with:</div>
            <div class="d-flex gap-2 mb-3">
                <label class="pay-method-btn flex-fill" style="cursor:pointer;">
                    <input type="radio" name="payment_method" value="gcash" class="d-none" required>
                    <div class="gcash-color fw-bold" style="font-size:.85rem;">GCash</div>
                </label>
                <label class="pay-method-btn flex-fill" style="cursor:pointer;">
                    <input type="radio" name="payment_method" value="maya" class="d-none" required>
                    <div class="maya-color fw-bold" style="font-size:.85rem;">Maya</div>
                </label>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-3" style="background:var(--primary);border:none;font-weight:600;">
                <i class="bi bi-credit-card me-1"></i> Pay Now
            </button>
        </form>
        @endif
    </div>
</div>
@endforeach
</div>

{{-- Payment History --}}
<div class="card mt-4">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Payment History</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Reference</th>
                        <th>Quarter</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($summary['payments']->where('status', 'paid') as $p)
                <tr>
                    <td class="ps-3" style="font-size:.8rem;font-weight:600;">{{ $p->payment_ref }}</td>
                    <td>Q{{ $p->quarter }}</td>
                    <td>₱{{ number_format($p->amount, 2) }}</td>
                    <td>{{ $p->payment_method_label }}</td>
                    <td><span class="badge rounded-pill px-2 {{ $p->status_badge_class }}">{{ ucfirst($p->status) }}</span></td>
                    <td style="font-size:.82rem;">{{ $p->paid_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No payments yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.pay-method-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('.payment-form');
        form.querySelectorAll('.pay-method-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type=radio]').checked = true;
    });
});
</script>
@endpush
