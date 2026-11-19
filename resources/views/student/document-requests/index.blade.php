@extends('layouts.student')
@section('title', 'Document Requests')

@section('content')
<div class="section-header">
    <div>
        <h2>Document Requests</h2>
        <p>Request official school documents online.</p>
    </div>
</div>

<div class="row g-4">
    {{-- Submit Request Form --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-plus me-2 text-primary"></i>Submit a Request</div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.document-requests.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <select name="document_type" id="docType" class="form-select @error('document_type') is-invalid @enderror" required
                                onchange="document.getElementById('otherDoc').style.display=this.value==='Other'?'block':'none'">
                            <option value="">Select document...</option>
                            @foreach(['Form 137','Good Moral Certificate','Enrollment Certificate','Diploma','Transcript of Records','Other'] as $dt)
                            <option value="{{ $dt }}" {{ old('document_type')===$dt?'selected':'' }}>{{ $dt }}</option>
                            @endforeach
                        </select>
                        @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="otherDoc" style="display:none;">
                        <label class="form-label">Specify Document</label>
                        <input type="text" name="other_document" class="form-control" value="{{ old('other_document') }}" placeholder="e.g., Authentication Letter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Copies</label>
                        <input type="number" name="copies" class="form-control" value="{{ old('copies', 1) }}" min="1" max="10" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Purpose / Reason</label>
                        <textarea name="purpose" rows="3" class="form-control @error('purpose') is-invalid @enderror"
                                  required placeholder="e.g., For college application, employment requirements...">{{ old('purpose') }}</textarea>
                        @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold text-white"
                            style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;padding:.7rem;">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </form>
                <div class="mt-3 p-3 rounded-3" style="background:#fffbeb;border:1px solid #fef3c7;">
                    <p class="mb-0" style="font-size:.8rem;color:#92400e;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Processing time is typically 3–5 school days. You will be notified when your document is ready for pickup.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Request History --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-clock-history me-2 text-primary"></i>My Requests</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8faff;">
                            <tr>
                                <th class="ps-3">Reference</th>
                                <th>Document</th>
                                <th>Copies</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="ps-3 fw-semibold text-primary" style="font-size:.82rem;">{{ $req->request_no }}</td>
                            <td style="font-size:.85rem;">{{ $req->document_label }}</td>
                            <td style="font-size:.85rem;">{{ $req->copies }}</td>
                            <td>
                                <span class="badge rounded-pill px-2 {{ $req->status_badge_class }}">
                                    <i class="bi {{ $req->status_icon }} me-1"></i>{{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td style="font-size:.8rem;color:#64748b;">{{ $req->created_at->format('M d, Y') }}</td>
                        </tr>
                        @if($req->admin_remarks)
                        <tr style="background:#f8fafc;">
                            <td colspan="5" class="ps-4 py-1" style="font-size:.78rem;color:#64748b;font-style:italic;">
                                <i class="bi bi-chat-left-text me-1"></i> Admin note: {{ $req->admin_remarks }}
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                            No document requests yet.
                        </td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Restore Other field visibility
document.addEventListener('DOMContentLoaded', function() {
    const docType = document.getElementById('docType');
    if (docType && docType.value === 'Other') {
        document.getElementById('otherDoc').style.display = 'block';
    }
});
</script>
@endpush
