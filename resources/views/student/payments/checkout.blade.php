<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Checkout | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .checkout-card { max-width:420px; width:100%; border-radius:20px; box-shadow:0 8px 40px rgba(0,0,0,.12); overflow:hidden; }
        .checkout-header { padding:2rem; text-align:center; color:#fff; }
        .checkout-header.gcash { background:linear-gradient(135deg,#007dfe,#005bb5); }
        .checkout-header.maya { background:linear-gradient(135deg,#00d632,#00a827); }
        .checkout-body { padding:2rem; background:#fff; }
        .amount-display { font-size:2.5rem; font-weight:800; color:#1e293b; text-align:center; margin:1rem 0; }
        .detail-row { display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #f1f5f9; font-size:.88rem; }
        .detail-row:last-child { border:none; }
        .btn-confirm { border:none; border-radius:12px; padding:.85rem; font-weight:700; font-size:1rem; width:100%; color:#fff; cursor:pointer; }
        .btn-confirm.gcash { background:#007dfe; }
        .btn-confirm.maya { background:#00d632; }
        .btn-cancel { background:transparent; border:1.5px solid #e2e8f0; border-radius:12px; padding:.75rem; font-weight:600; width:100%; color:#64748b; cursor:pointer; margin-top:.75rem; }
        .demo-badge { background:#fef9c3; color:#a16207; font-size:.72rem; padding:.25rem .6rem; border-radius:20px; font-weight:600; }
    </style>
</head>
<body>
<div class="checkout-card">
    <div class="checkout-header {{ $method }}">
        @if($method === 'gcash')
        <div style="font-size:2rem;font-weight:800;letter-spacing:-1px;">GCash</div>
        @else
        <div style="font-size:2rem;font-weight:800;letter-spacing:-1px;">Maya</div>
        @endif
        <div style="opacity:.8;font-size:.85rem;margin-top:.25rem;">Secure Online Payment</div>
        <div class="mt-2"><span class="demo-badge">DEMO MODE</span></div>
    </div>
    <div class="checkout-body">
        <div class="text-center text-muted" style="font-size:.82rem;">Amount to Pay</div>
        <div class="amount-display">₱{{ number_format($record->amount, 2) }}</div>

        <div class="mb-4">
            <div class="detail-row"><span class="text-muted">Reference</span><span class="fw-semibold" style="font-size:.8rem;">{{ $record->payment_ref }}</span></div>
            <div class="detail-row"><span class="text-muted">Quarter</span><span class="fw-semibold">Quarter {{ $record->quarter }}</span></div>
            <div class="detail-row"><span class="text-muted">School Year</span><span class="fw-semibold">SY {{ $record->schoolYear->year_label }}</span></div>
            <div class="detail-row"><span class="text-muted">Payment Method</span><span class="fw-semibold">{{ $method === 'gcash' ? 'GCash' : 'Maya' }}</span></div>
        </div>

        <form method="POST" action="{{ route('student.payments.process', $record->payment_ref) }}">
            @csrf
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn-confirm {{ $method }}">
                <i class="bi bi-shield-check me-1"></i> Confirm Payment
            </button>
        </form>
        <form method="POST" action="{{ route('student.payments.process', $record->payment_ref) }}">
            @csrf
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn-cancel">Cancel Payment</button>
        </form>

        <div class="text-center mt-3" style="font-size:.72rem;color:#94a3b8;">
            <i class="bi bi-lock-fill me-1"></i> This is a demo checkout. In production, you will be redirected to the actual {{ $method === 'gcash' ? 'GCash' : 'Maya' }} app.
        </div>
    </div>
</div>
</body>
</html>
