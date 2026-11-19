@extends('layouts.parent')
@section('title', 'Child Grades')

@section('content')
@if(!$child)
    <div class="alert alert-warning">Please connect a child account first.</div>
@else
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Academic Grades — {{ $child->full_name }}</h4>
        <div class="text-muted small">
            LRN: {{ $child->lrn }}
            @if($enrollment) &bull; {{ $enrollment->grade_level }} {{ $enrollment->strand }} @endif
            &bull; SY {{ $sy?->year_label ?? 'N/A' }}
        </div>
    </div>

    @forelse($gradesByQuarter as $quarter => $entries)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Quarter {{ $quarter }}</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill">
                Average: {{ number_format($entries->avg('grade'), 2) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Subject</th>
                            <th class="text-center">Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $entry->subject?->subject_code ?? '—' }}</td>
                            <td>{{ $entry->subject?->subject_name ?? 'Subject' }}</td>
                            <td class="text-center fw-bold">{{ number_format($entry->grade, 0) }}</td>
                            <td>
                                @if($entry->grade >= 75)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Passed</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Failed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-award fs-1 mb-2 opacity-25"></i>
        No published grades available yet for SY {{ $sy?->year_label ?? 'N/A' }}.
    </div>
    @endforelse
@endif
@endsection
