<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $sy      = SchoolYear::active();
        $syId    = $request->get('sy', $sy?->id ?? 0);
        $sections = Section::with('schoolYear')
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q->where('status', 'Enrolled')])
            ->where('school_year_id', $syId)
            ->orderBy('grade_level')->orderBy('strand')->orderBy('section_name')
            ->get();
        $schoolYears = SchoolYear::orderByDesc('id')->get();
        return view('admin.sections.index', compact('sections', 'schoolYears', 'sy', 'syId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name'   => 'required|string|max:100',
            'grade_level'    => 'required|string',
            'school_year_id' => 'required|exists:school_years,id',
            'max_capacity'   => 'required|integer|min:1|max:100',
        ]);
        Section::create($request->only(['section_name','grade_level','track','strand','max_capacity','school_year_id']));
        return back()->with('success', 'Section added.');
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'section_name' => 'required|string|max:100',
            'grade_level'  => 'required|string',
            'max_capacity' => 'required|integer|min:1|max:100',
        ]);
        $section->update($request->only(['section_name','grade_level','track','strand','max_capacity','school_year_id']));
        return back()->with('success', 'Section updated.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return back()->with('success', 'Section deleted.');
    }
}
