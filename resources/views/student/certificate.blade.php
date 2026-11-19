<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Certificate — {{ $student->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; margin: 0; padding: 2rem; }
        .page { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.1); overflow: hidden; }
        .cert-border { border: 8px solid #1a3c6e; border-radius: 16px; margin: 12px; }
        .cert-inner { border: 2px solid #c0a945; border-radius: 10px; padding: 2.5rem; }
        .cert-header { text-align: center; margin-bottom: 1.5rem; }
        .cert-logo { width: 80px; height: 80px; margin: 0 auto .75rem; border-radius: 50%; overflow: hidden; border: 3px solid #1a3c6e; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
        .cert-logo img { width: 100%; height: 100%; object-fit: cover; }
        .cert-school { font-size: 1.3rem; font-weight: 800; color: #1a3c6e; letter-spacing: 1px; text-transform: uppercase; margin: 0; }
        .cert-address { font-size: .78rem; color: #64748b; margin: .25rem 0 0; }
        .divider { border: none; border-top: 2px solid #c0a945; margin: 1.25rem 0; }
        .cert-title { text-align: center; font-size: 1.6rem; font-weight: 800; color: #1a3c6e; text-transform: uppercase; letter-spacing: 3px; margin: .5rem 0 .25rem; }
        .cert-subtitle { text-align: center; font-size: .78rem; color: #94a3b8; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 2rem; }
        .cert-body { font-size: .98rem; color: #334155; line-height: 1.9; text-align: justify; margin-bottom: 2rem; }
        .cert-name { font-size: 1.5rem; font-weight: 800; color: #1a3c6e; text-align: center; margin: .5rem 0; display: block; }
        .cert-detail { font-size: .95rem; text-align: center; }
        .cert-table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .cert-table td { padding: .45rem .75rem; font-size: .88rem; }
        .cert-table td:first-child { font-weight: 600; color: #64748b; width: 45%; text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; }
        .cert-table td:last-child { font-weight: 700; color: #1e293b; }
        .cert-footer { display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; margin-top: 2.5rem; }
        .sig-block { text-align: center; flex: 1; }
        .sig-name { font-weight: 800; font-size: .92rem; color: #1a3c6e; margin-bottom: 2px; }
        .sig-title { font-size: .72rem; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .sig-line { border-top: 1.5px solid #1a3c6e; margin-bottom: .4rem; padding-top: 4px; }
        .stamp-area { width: 90px; height: 90px; border: 2px dashed #c0a945; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #c0a945; font-size: .65rem; text-align: center; opacity: .5; }
        .ref-no { font-size: .72rem; color: #94a3b8; text-align: center; margin-top: 1rem; }
        .print-actions { display: flex; justify-content: center; gap: 1rem; margin-bottom: 1.5rem; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: .88rem; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #1a3c6e, #2356a0); color: #fff; }
        .btn-outline { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-actions { display: none !important; }
            .page { box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline">← Dashboard</a>
    <button onclick="window.print()" class="btn btn-primary">🖨 Print Certificate</button>
</div>

<div class="page">
    <div class="cert-border">
        <div class="cert-inner">
            <div class="cert-header">
                <div class="cert-logo">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    @elseif(file_exists(public_path('images/logo.jpg')))
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo">
                    @else
                        🏫
                    @endif
                </div>
                <h1 class="cert-school">{{ \App\Models\Setting::get('school_name', 'Sta. Lucia Academy') }}</h1>
                <p class="cert-address">{{ \App\Models\Setting::get('school_address', '') }}</p>
            </div>

            <hr class="divider">

            <div class="cert-title">Certificate of Enrollment</div>
            <div class="cert-subtitle">Official Document</div>

            <div class="cert-body">
                <p>This is to certify that:</p>
                <span class="cert-name">{{ strtoupper($student->full_name) }}</span>
                <p class="cert-detail" style="color:#64748b;font-size:.85rem;margin:.25rem 0 1rem;">
                    {{ $student->lrn ? 'LRN: ' . $student->lrn : '' }}
                </p>
                <p>is officially enrolled at <strong>{{ \App\Models\Setting::get('school_name', 'Sta. Lucia Academy') }}</strong> for the
                school year <strong>{{ $enrollment->schoolYear->year_label }}</strong>.</p>
            </div>

            <table class="cert-table">
                <tr><td>Grade Level</td><td>{{ $enrollment->grade_level }}</td></tr>
                @if($enrollment->strand)<tr><td>Strand / Track</td><td>{{ $enrollment->track }} — {{ $enrollment->strand }}</td></tr>@endif
                @if($enrollment->semester)<tr><td>Semester</td><td>{{ $enrollment->semester }}</td></tr>@endif
                @if($enrollment->section)<tr><td>Section</td><td>{{ $enrollment->section->section_name }}</td></tr>@endif
                <tr><td>Enrollment No.</td><td>{{ $enrollment->enrollment_no }}</td></tr>
                <tr><td>Date Enrolled</td><td>{{ $enrollment->enrollment_date->format('F d, Y') }}</td></tr>
                <tr><td>Enrollment Status</td><td><strong style="color:#15803d;">{{ $enrollment->status }}</strong></td></tr>
            </table>

            <p style="font-size:.88rem;color:#475569;">
                This certification is issued upon the request of the student concerned and for whatever legal purpose it may serve.
            </p>

            <div class="cert-footer">
                <div class="sig-block">
                    <div class="sig-line">
                        <div class="sig-name">{{ \App\Models\Setting::get('school_registrar', '________________________________') }}</div>
                    </div>
                    <div class="sig-title">Registrar</div>
                </div>
                <div class="stamp-area">DRY SEAL HERE</div>
                <div class="sig-block">
                    <div class="sig-line">
                        <div class="sig-name">{{ \App\Models\Setting::get('school_principal', '________________________________') }}</div>
                    </div>
                    <div class="sig-title">Principal / Administrator</div>
                </div>
            </div>

            <div class="ref-no">
                Date Issued: {{ now()->format('F d, Y') }} &nbsp;|&nbsp; Ref: {{ $enrollment->enrollment_no }}-CERT
            </div>
        </div>
    </div>
</div>

</body>
</html>
