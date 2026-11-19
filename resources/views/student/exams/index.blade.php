@extends('layouts.student')
@section('title', 'Quarterly Examinations')

@section('content')
<div class="section-header">
    <div>
        <h2>Quarterly Examinations</h2>
        <p class="text-muted mb-0">Exam schedule — payment required before each quarter exam</p>
    </div>
</div>

@if(!$enrollment)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-journal-text fs-1 text-muted opacity-25 d-block mb-3"></i>
        <h5>No Active Enrollment</h5>
        <p class="text-muted">Exam schedules will appear here once you are enrolled.</p>
    </div>
</div>
@else
<div class="alert border-0 rounded-3 mb-4" style="background:#fef9c3;color:#a16207;">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Important:</strong> You must pay the quarterly tuition fee before you can take the corresponding examination.
    <a href="{{ route('student.payments.index') }}" class="fw-bold ms-1">Go to Payments →</a>
</div>

{{-- Payment Status Quick View --}}
<div class="row g-3 mb-4">
@foreach($summary['payments'] as $payment)
<div class="col-6 col-md-3">
    <div class="card text-center p-3 {{ $payment->isPaid() ? 'border-success' : 'border-warning' }}" style="border-width:2px;">
        <div class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Q{{ $payment->quarter }} Payment</div>
        <div class="mt-1">
            @if($payment->isPaid())
            <i class="bi bi-check-circle-fill text-success fs-4"></i>
            <div class="text-success fw-bold" style="font-size:.82rem;">Paid</div>
            @else
            <i class="bi bi-x-circle-fill text-warning fs-4"></i>
            <div class="text-warning fw-bold" style="font-size:.82rem;">Unpaid</div>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>

<div class="row g-3">
@forelse($exams as $exam)
<div class="col-md-6">
    <div class="card h-100 {{ $exam->can_take ? '' : 'opacity-75' }}" style="{{ !$exam->can_take ? 'border:2px dashed #e2e8f0;' : '' }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge rounded-pill px-2 mb-1" style="background:#eff6ff;color:#1d4ed8;">Q{{ $exam->quarter }}</span>
                    <h5 class="mb-0" style="font-size:1rem;font-weight:700;">{{ $exam->title }}</h5>
                </div>
                @if($exam->can_take)
                <span class="badge rounded-pill badge-enrolled px-2"><i class="bi bi-unlock me-1"></i>Eligible</span>
                @else
                <span class="badge rounded-pill badge-pending px-2"><i class="bi bi-lock me-1"></i>Locked</span>
                @endif
            </div>

            <div class="text-muted mb-3" style="font-size:.85rem;">
                <div><i class="bi bi-calendar3 me-2"></i>{{ $exam->exam_date->format('l, M d, Y') }}</div>
                @if($exam->start_time)
                <div><i class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}
                    @if($exam->end_time) – {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}@endif
                </div>
                @endif
                @if($exam->venue)
                <div><i class="bi bi-geo-alt me-2"></i>{{ $exam->venue }}</div>
                @endif
            </div>

            @if($exam->instructions)
            <div class="p-2 rounded-3 mb-3" style="background:#f8faff;font-size:.82rem;">{{ $exam->instructions }}</div>
            @endif

            @if(!$exam->can_take)
            <div class="alert alert-warning border-0 rounded-3 mb-0 py-2" style="font-size:.82rem;">
                <i class="bi bi-lock-fill me-1"></i>
                Pay Quarter {{ $exam->quarter }} fee to unlock this exam.
                <a href="{{ route('student.payments.index') }}" class="fw-bold">Pay Now</a>
            </div>
            @else
            <div class="text-success" style="font-size:.82rem;">
                <i class="bi bi-check-circle-fill me-1"></i> You are eligible to take this examination.
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-25"></i>
            No examination schedules available for {{ $enrollment->grade_level }}.
        </div>
    </div>
</div>
@endforelse
</div>
@endif
@endsection
