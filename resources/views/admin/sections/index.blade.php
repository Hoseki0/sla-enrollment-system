@extends('layouts.admin')
@section('title', 'Sections')

@section('content')
<div class="section-header">
    <div>
        <h2><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Sections</h2>
        <p>Manage sections for each grade level and school year.</p>
    </div>
    <button class="btn text-white fw-semibold px-3 rounded-pill"
            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);"
            data-bs-toggle="modal" data-bs-target="#sectionModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i>Add Section
    </button>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label mb-1" style="font-size:.8rem;">School Year</label>
                <select name="sy" class="form-select form-select-sm">
                    @foreach($schoolYears as $s)
                    <option value="{{ $s->id }}" {{ $syId == $s->id ? 'selected' : '' }}>
                        SY {{ $s->year_label }} {{ $s->is_active ? '(Active)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Filter</button>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
        </form>
    </div>
</div>

{{-- Cards Grid --}}
<div class="row g-3">
@forelse($sections as $sec)
@php
    $pct      = $sec->max_capacity > 0 ? round(($sec->enrolled_count / $sec->max_capacity) * 100) : 0;
    $barColor = $pct >= 90 ? '#dc2626' : ($pct >= 70 ? '#ca8a04' : '#15803d');
    $isSHS    = in_array($sec->grade_level, ['Grade 11','Grade 12']);
@endphp
<div class="col-md-4 col-lg-3">
    <div class="card h-100" style="border-radius:14px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-bold" style="font-size:.95rem;">{{ $sec->section_name }}</div>
                    <div style="font-size:.78rem;color:#64748b;">{{ $sec->grade_level }}</div>
                </div>
                <span class="badge rounded-pill {{ $isSHS ? 'badge-shs' : 'badge-jhs' }}" style="font-size:.7rem;">
                    {{ $isSHS ? 'SHS' : 'JHS' }}
                </span>
            </div>
            @if($sec->strand)
            <div class="mb-2">
                <span class="badge rounded-pill px-2" style="background:#f5f3ff;color:#6d28d9;font-size:.72rem;">
                    {{ $sec->track }} · {{ $sec->strand }}
                </span>
            </div>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:.78rem;color:#64748b;">
                <span>Enrollment</span>
                <span class="fw-semibold">{{ $sec->enrolled_count }} / {{ $sec->max_capacity }}</span>
            </div>
            <div class="progress mb-3" style="height:6px;border-radius:10px;background:#f1f5f9;">
                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $barColor }};border-radius:10px;"></div>
            </div>
            <div class="d-flex gap-1">
                <button class="btn btn-sm flex-fill rounded-pill py-1 edit-section-btn"
                        data-id="{{ $sec->id }}"
                        data-name="{{ $sec->section_name }}"
                        data-grade="{{ $sec->grade_level }}"
                        data-track="{{ $sec->track }}"
                        data-strand="{{ $sec->strand }}"
                        data-cap="{{ $sec->max_capacity }}"
                        data-sy="{{ $sec->school_year_id }}"
                        style="font-size:.75rem;background:#fefce8;color:#a16207;"
                        data-bs-toggle="modal" data-bs-target="#sectionModal">
                    <i class="bi bi-pencil me-1"></i>Edit
                </button>
                <form method="POST" action="{{ route('admin.sections.destroy', $sec) }}" class="flex-fill"
                      onsubmit="return confirm('Delete this section? Students assigned here will be unassigned.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm w-100 rounded-pill py-1"
                            style="font-size:.75rem;background:#fee2e2;color:#dc2626;">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="card text-center py-5">
        <div class="text-muted"><i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-25"></i>No sections found for this school year.</div>
    </div>
</div>
@endforelse
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="modalTitle">Add Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="sectionForm" action="{{ route('admin.sections.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">School Year</label>
                            <select name="school_year_id" id="modal_sy" class="form-select">
                                @foreach($schoolYears as $s)
                                <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>
                                    SY {{ $s->year_label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                            <select name="grade_level" id="modal_grade" class="form-select" required onchange="toggleModalSHS()">
                                <option value="">-- Select --</option>
                                @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" name="section_name" id="modal_name" class="form-control"
                                   placeholder="e.g. Section 1" required>
                        </div>
                        <div class="col-md-6" id="modal_track_div" style="display:none;">
                            <label class="form-label">Track</label>
                            <select name="track" id="modal_track" class="form-select" onchange="updateModalStrands()">
                                <option value="">-- Select Track --</option>
                                @foreach(['Academic','TVL','Sports','Arts & Design'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="modal_strand_div" style="display:none;">
                            <label class="form-label">Strand</label>
                            <select name="strand" id="modal_strand" class="form-select">
                                <option value="">-- Select Strand --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Capacity</label>
                            <input type="number" name="max_capacity" id="modal_cap" class="form-control"
                                   value="40" min="1" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4" style="background:#1a3c6e;">Save Section</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const shsStrands = {
    'Academic':     ['STEM','ABM','HUMSS','GAS','TVL Integration'],
    'TVL':          ['ICT','HE','Agri-Fishery','Industrial Arts'],
    'Sports':       ['Sports Track'],
    'Arts & Design':['Arts & Design Track'],
};

function resetForm() {
    document.getElementById('modalTitle').textContent = 'Add Section';
    document.getElementById('sectionForm').action    = '{{ route("admin.sections.store") }}';
    document.getElementById('formMethod').value      = 'POST';
    document.getElementById('modal_name').value  = '';
    document.getElementById('modal_grade').value = '';
    document.getElementById('modal_cap').value   = '40';
    document.getElementById('modal_track').value = '';
    document.getElementById('modal_strand').innerHTML = '<option value="">-- Select Strand --</option>';
    document.getElementById('modal_track_div').style.display  = 'none';
    document.getElementById('modal_strand_div').style.display = 'none';
}

function toggleModalSHS() {
    const grade = document.getElementById('modal_grade').value;
    const isSHS = ['Grade 11','Grade 12'].includes(grade);
    document.getElementById('modal_track_div').style.display  = isSHS ? '' : 'none';
    document.getElementById('modal_strand_div').style.display = isSHS ? '' : 'none';
}

function updateModalStrands() {
    const track = document.getElementById('modal_track').value;
    const sel   = document.getElementById('modal_strand');
    sel.innerHTML = '<option value="">-- Select Strand --</option>';
    (shsStrands[track] || []).forEach(s => {
        const o = document.createElement('option'); o.value = o.textContent = s; sel.appendChild(o);
    });
}

document.querySelectorAll('.edit-section-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        document.getElementById('modalTitle').textContent = 'Edit Section';
        document.getElementById('sectionForm').action    = `/admin/sections/${id}`;
        document.getElementById('formMethod').value      = 'PUT';
        document.getElementById('modal_name').value   = this.dataset.name;
        document.getElementById('modal_grade').value  = this.dataset.grade;
        document.getElementById('modal_cap').value    = this.dataset.cap;
        document.getElementById('modal_sy').value     = this.dataset.sy;
        toggleModalSHS();
        document.getElementById('modal_track').value  = this.dataset.track;
        updateModalStrands();
        document.getElementById('modal_strand').value = this.dataset.strand;
    });
});
</script>
@endpush
