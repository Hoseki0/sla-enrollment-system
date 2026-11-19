@extends('layouts.admin')
@section('title', 'Document Requests')

@section('content')
<div class="section-header">
    <div>
        <h2>Document Requests</h2>
        <p>Manage student requests for official school documents.</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php $statCards = [
        ['label'=>'Pending',    'val'=>$stats['pending'],    'color'=>'#a16207,#ca8a04', 'icon'=>'bi-hourglass-split'],
        ['label'=>'Processing', 'val'=>$stats['processing'], 'color'=>'#1d4ed8,#2563eb', 'icon'=>'bi-arrow-repeat'],
        ['label'=>'Ready',      'val'=>$stats['ready'],      'color'=>'#15803d,#16a34a', 'icon'=>'bi-check-circle'],
        ['label'=>'Released',   'val'=>$stats['released'],   'color'=>'#1a3c6e,#2356a0', 'icon'=>'bi-box-seam'],
    ]; @endphp
    @foreach($statCards as $c)
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,{{ $c['color'] }});">
            <div class="stat-icon" style="background:rgba(255,255,255,.15);">
                <i class="bi {{ $c['icon'] }} text-white"></i>
            </div>
            <div>
                <div class="stat-val text-white">{{ $c['val'] }}</div>
                <div class="stat-label">{{ $c['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            @foreach(['pending','processing','ready','released','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="document_type" class="form-select form-select-sm">
            <option value="">All Types</option>
            @foreach(['Form 137','Good Moral Certificate','Enrollment Certificate','Diploma','Transcript of Records','Other'] as $dt)
            <option value="{{ $dt }}" {{ request('document_type')===$dt?'selected':'' }}>{{ $dt }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('admin.document-requests.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8faff;">
                    <tr>
                        <th class="ps-3">Reference No.</th>
                        <th>Student</th>
                        <th>Document</th>
                        <th>Copies</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th class="text-end pe-3">Update Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                <tr>
                    <td class="ps-3 fw-semibold text-primary" style="font-size:.82rem;">{{ $req->request_no }}</td>
                    <td class="fw-semibold" style="font-size:.87rem;">{{ $req->student->full_name }}</td>
                    <td style="font-size:.85rem;">{{ $req->document_label }}</td>
                    <td style="font-size:.85rem;">{{ $req->copies }}</td>
                    <td style="font-size:.8rem;color:#64748b;max-width:160px;">{{ Str::limit($req->purpose, 50) }}</td>
                    <td>
                        <span class="badge rounded-pill px-2 {{ $req->status_badge_class }}">
                            <i class="bi {{ $req->status_icon }} me-1"></i>{{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $req->created_at->format('M d, Y') }}</td>
                    <td class="text-end pe-3">
                        <form method="POST" action="{{ route('admin.document-requests.updateStatus', $req) }}" class="d-flex gap-1 justify-content-end">
                            @csrf @method('PATCH')
                            <select name="status" class="form-select form-select-sm" style="width:auto;font-size:.78rem;">
                                @foreach(['pending','processing','ready','released','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $req->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-primary" style="font-size:.75rem;">Update</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No document requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $requests->links() }}
@endsection
