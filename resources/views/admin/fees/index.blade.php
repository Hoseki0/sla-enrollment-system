@extends('layouts.admin')
@section('title', 'Fee Schedules')

@section('content')
<div class="section-header">
    <div>
        <h2>Fee Schedules</h2>
        <p>Configure quarterly tuition fees per grade level — SY {{ $sy?->year_label ?? 'N/A' }}</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Set All 4 Quarters</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.fees.bulk') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" required>
                            @foreach($gradeLevels as $gl)
                            <option value="{{ $gl }}">{{ $gl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount per Quarter (₱)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="500.00">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3" style="background:var(--primary);border:none;">
                        Set All 4 Quarters
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-pencil me-2"></i>Set Single Quarter</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.fees.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select" required>
                                @foreach($gradeLevels as $gl)
                                <option value="{{ $gl }}">{{ $gl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Quarter</label>
                            <select name="quarter" class="form-select" required>
                                @for($q=1;$q<=4;$q++)<option value="{{ $q }}">Q{{ $q }}</option>@endfor
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary w-100 rounded-3">Save Quarter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Current Fee Schedules</div>
            <div class="card-body p-0">
                @forelse($schedules as $gradeLevel => $fees)
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <h6 class="fw-bold mb-2">{{ $gradeLevel }}</h6>
                    <div class="row g-2">
                        @foreach($fees->sortBy('quarter') as $fee)
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded-3 text-center" style="background:#f8faff;border:1px solid #e2e8f0;">
                                <div class="text-muted" style="font-size:.7rem;font-weight:600;">Q{{ $fee->quarter }}</div>
                                <div class="fw-bold" style="color:var(--primary);">₱{{ number_format($fee->amount, 2) }}</div>
                                @if($fee->due_date)
                                <div class="text-muted" style="font-size:.68rem;">Due {{ $fee->due_date->format('M d') }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">No fee schedules configured yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
