<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card — {{ $enrollment->student->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 2rem; }
        .report-card { max-width: 820px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }
        .rc-header { background: linear-gradient(135deg, #001f3f, #003366); color: #fff; padding: 2rem; text-align: center; }
        .rc-header h1 { font-size: 1.4rem; font-weight: 800; margin: 0 0 .25rem; letter-spacing: 1px; }
        .rc-header p  { font-size: .85rem; opacity: .7; margin: 0; }
        .rc-badge { display: inline-block; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); border-radius: 8px; padding: .4rem 1rem; font-size: .78rem; font-weight: 600; margin-top: .75rem; letter-spacing: .5px; }
        .rc-info { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.5rem; background: #f8faff; border-bottom: 1px solid #e2e8f0; }
        .rc-info-item { }
        .rc-info-label { font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
        .rc-info-value { font-size: .95rem; font-weight: 600; color: #1e293b; margin-top: 2px; }
        .rc-grades { padding: 1.5rem; }
        .rc-grades h3 { font-size: 1rem; font-weight: 700; color: #1a3c6e; margin: 0 0 1rem; border-bottom: 2px solid #eff6ff; padding-bottom: .5rem; }
        table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        thead th { background: #1a3c6e; color: #fff; padding: .65rem .85rem; text-align: left; font-size: .75rem; font-weight: 600; letter-spacing: .3px; }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody td { padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; }
        .grade-val { font-weight: 700; font-size: 1rem; }
        .grade-pass { color: #15803d; }
        .grade-fail { color: #dc2626; }
        .grade-na   { color: #94a3b8; }
        .avg-row td { background: #eff6ff; font-weight: 700; }
        .rc-footer { padding: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; gap: 2rem; }
        .sig-line { text-align: center; flex: 1; }
        .sig-line .line { border-bottom: 1.5px solid #1a3c6e; margin-bottom: .4rem; height: 40px; }
        .sig-line .label { font-size: .72rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .rc-print-btn { display: flex; justify-content: center; gap: 1rem; margin-bottom: 1.5rem; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: .88rem; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #1a3c6e, #2356a0); color: #fff; }
        .btn-outline { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
        @media print {
            body { background: #fff; padding: .5rem; }
            .rc-print-btn, .btn { display: none !important; }
            .report-card { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>

<div class="rc-print-btn">
    <a href="{{ route('admin.grades.show', $enrollment) }}" class="btn btn-outline">← Back</a>
    <button onclick="window.print()" class="btn btn-primary">🖨 Print / Save PDF</button>
</div>

<div class="report-card">
    <div class="rc-header">
        <h1>{{ \App\Models\Setting::get('school_name', 'STA. LUCIA ACADEMY') }}</h1>
        <p>{{ \App\Models\Setting::get('school_address', '') }}</p>
        <div class="rc-badge">STUDENT REPORT CARD</div>
    </div>

    <div class="rc-info">
        <div class="rc-info-item">
            <div class="rc-info-label">Student Name</div>
            <div class="rc-info-value">{{ $enrollment->student->full_name }}</div>
        </div>
        <div class="rc-info-item">
            <div class="rc-info-label">LRN</div>
            <div class="rc-info-value">{{ $enrollment->student->lrn ?? 'N/A' }}</div>
        </div>
        <div class="rc-info-item">
            <div class="rc-info-label">Grade Level</div>
            <div class="rc-info-value">{{ $enrollment->grade_level }}
                @if($enrollment->strand) — {{ $enrollment->strand }} @endif
            </div>
        </div>
        <div class="rc-info-item">
            <div class="rc-info-label">Section</div>
            <div class="rc-info-value">{{ $enrollment->section?->section_name ?? 'N/A' }}</div>
        </div>
        <div class="rc-info-item">
            <div class="rc-info-label">School Year</div>
            <div class="rc-info-value">SY {{ $enrollment->schoolYear->year_label }}</div>
        </div>
        <div class="rc-info-item">
            <div class="rc-info-label">Enrollment No.</div>
            <div class="rc-info-value">{{ $enrollment->enrollment_no }}</div>
        </div>
    </div>

    <div class="rc-grades">
        <h3>Academic Performance</h3>
        @if($gradeEntries->isEmpty())
            <p style="text-align:center;color:#94a3b8;padding:2rem;">No published grades available.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th style="text-align:center;">Q1</th>
                    <th style="text-align:center;">Q2</th>
                    <th style="text-align:center;">Q3</th>
                    <th style="text-align:center;">Q4</th>
                    <th style="text-align:center;">Final Average</th>
                    <th style="text-align:center;">Remarks</th>
                </tr>
            </thead>
            <tbody>
            @php $overallSum = 0; $overallCount = 0; @endphp
            @foreach($gradeEntries as $subject => $entries)
                @php
                    $grades = $entries->pluck('grade', 'quarter');
                    $avg = $grades->filter()->avg();
                    $overallSum += $avg;
                    $overallCount++;
                @endphp
                <tr>
                    <td>{{ $subject }}</td>
                    @for ($q = 1; $q <= 4; $q++)
                    <td style="text-align:center;">
                        @php $g = $grades[$q] ?? null; @endphp
                        @if($g !== null)
                            <span class="grade-val {{ $g >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ number_format($g, 0) }}</span>
                        @else
                            <span class="grade-na">—</span>
                        @endif
                    </td>
                    @endfor
                    <td style="text-align:center;">
                        @if($avg)
                            <span class="grade-val {{ $avg >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ number_format($avg, 2) }}</span>
                        @else <span class="grade-na">—</span> @endif
                    </td>
                    <td style="text-align:center;">
                        @if($avg)
                            <span style="font-weight:600;font-size:.78rem;color:{{ $avg >= 75 ? '#15803d' : '#dc2626' }}">
                                {{ $avg >= 75 ? 'PASSED' : 'FAILED' }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if($overallCount > 0)
            <tr class="avg-row">
                <td colspan="5" style="text-align:right;">General Average:</td>
                <td style="text-align:center;">
                    @php $gwa = $overallSum / $overallCount; @endphp
                    <span class="grade-val {{ $gwa >= 75 ? 'grade-pass' : 'grade-fail' }}">
                        {{ number_format($gwa, 2) }}
                    </span>
                </td>
                <td style="text-align:center;">
                    <span style="font-weight:700;color:{{ $gwa >= 75 ? '#15803d' : '#dc2626' }}">
                        {{ $gwa >= 75 ? 'PROMOTED' : 'FOR REMEDIATION' }}
                    </span>
                </td>
            </tr>
            @endif
            </tbody>
        </table>
        @endif
    </div>

    <div class="rc-footer">
        <div class="sig-line">
            <div class="line"></div>
            <div class="label">Class Adviser / Teacher</div>
        </div>
        <div class="sig-line">
            <div class="line"></div>
            <div class="label">Registrar</div>
        </div>
        <div class="sig-line">
            <div class="line"></div>
            <div class="label">Principal / Administrator</div>
        </div>
    </div>
</div>

</body>
</html>
