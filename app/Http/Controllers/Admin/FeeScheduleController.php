<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeSchedule;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class FeeScheduleController extends Controller
{
    public function index()
    {
        $sy = SchoolYear::active();

        $schedules = FeeSchedule::when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
            ->orderBy('grade_level')
            ->orderBy('quarter')
            ->get()
            ->groupBy('grade_level');

        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        return view('admin.fees.index', compact('schedules', 'sy', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $sy = SchoolYear::active();
        if (!$sy) {
            return back()->with('error', 'No active school year.');
        }

        $data = $request->validate([
            'grade_level' => 'required|string',
            'quarter'     => 'required|integer|min:1|max:4',
            'amount'      => 'required|numeric|min:0',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        FeeSchedule::updateOrCreate(
            [
                'school_year_id' => $sy->id,
                'grade_level'    => $data['grade_level'],
                'quarter'        => $data['quarter'],
            ],
            [
                'amount'      => $data['amount'],
                'due_date'    => $data['due_date'] ?? null,
                'description' => $data['description'] ?? null,
            ]
        );

        return back()->with('success', "Fee schedule for {$data['grade_level']} Q{$data['quarter']} saved.");
    }

    public function bulkStore(Request $request)
    {
        $sy = SchoolYear::active();
        if (!$sy) {
            return back()->with('error', 'No active school year.');
        }

        $data = $request->validate([
            'grade_level' => 'required|string',
            'amount'      => 'required|numeric|min:0',
        ]);

        for ($q = 1; $q <= 4; $q++) {
            $dueMonth = match ($q) {
                1 => 8, 2 => 11, 3 => 2, 4 => 5,
            };
            $dueYear = $q <= 2 ? (int) explode('-', $sy->year_label)[0] : (int) explode('-', $sy->year_label)[1];

            FeeSchedule::updateOrCreate(
                [
                    'school_year_id' => $sy->id,
                    'grade_level'    => $data['grade_level'],
                    'quarter'        => $q,
                ],
                [
                    'amount'      => $data['amount'],
                    'due_date'    => sprintf('%d-%02d-15', $dueYear, $dueMonth),
                    'description' => "Quarter {$q} tuition fee",
                ]
            );
        }

        return back()->with('success', "All 4 quarters for {$data['grade_level']} set to ₱" . number_format($data['amount'], 2));
    }
}
