<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::withCount('enrollments')->orderByDesc('id')->get();
        return view('admin.school-years.index', compact('schoolYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_label' => ['required', 'regex:/^\d{4}-\d{4}$/', 'unique:school_years,year_label'],
        ], [
            'year_label.regex' => 'Year format must be YYYY-YYYY (e.g. 2026-2027).',
        ]);
        SchoolYear::create(['year_label' => $request->year_label, 'is_active' => false]);
        return back()->with('success', 'School year SY ' . $request->year_label . ' added.');
    }

    public function activate(SchoolYear $schoolYear)
    {
        $schoolYear->setActive();
        return back()->with('success', 'SY ' . $schoolYear->year_label . ' is now active.');
    }
}
