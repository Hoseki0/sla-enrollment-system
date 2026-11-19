@extends('layouts.admin')
@section('title', 'Encode Grades')

@section('content')
<div class="section-header">
    <div>
        <h2>Encode Grades</h2>
        <p>{{ $enrollment->student->full_name }} — {{ $enrollment->grade_level }} @if($enrollment->strand)· {{ $enrollment->strand }}@endif</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('admin.grades.publish', $enrollment) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success rounded-3" onclick="return confirm('Publish all grades for this student?')">
                <i class="bi bi-send me-1"></i> Publish All
            </button>
        </form>
        <a href="{{ route('admin.grades.index') }}" class="btn btn-outline-secondary rounded-3">← Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Subject</th>
                        @for($q=1;$q<=4;$q++)
                        <th class="text-center">Q{{ $q }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                @foreach($subjects as $subject)
                <tr>
                    <td class="ps-3">
                        <div style="font-size:.85rem;font-weight:600;">{{ $subject->subject_name }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $subject->subject_code }}</div>
                    </td>
                    @for($q=1;$q<=4;$q++)
                    @php $key = $subject->id . '-' . $q; $entry = $entries->get($key)?->first(); @endphp
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.grades.store', $enrollment) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <input type="hidden" name="quarter" value="{{ $q }}">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <input type="number" name="grade" value="{{ $entry?->grade }}"
                                       class="form-control form-control-sm text-center" style="width:70px;"
                                       min="0" max="100" step="0.01" placeholder="—">
                                <select name="status" class="form-select form-select-sm" style="width:80px;font-size:.7rem;">
                                    <option value="draft" {{ ($entry?->status ?? 'draft')==='draft'?'selected':'' }}>Draft</option>
                                    <option value="published" {{ ($entry?->status)==='published'?'selected':'' }}>Published</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary" style="font-size:.68rem;padding:.15rem .4rem;">Save</button>
                            </div>
                        </form>
                    </td>
                    @endfor
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
