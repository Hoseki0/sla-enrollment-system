<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentRequest;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $student  = Auth::guard('student')->user();
        $requests = DocumentRequest::where('student_id', $student->id)
            ->latest()
            ->get();

        $enrollment = $student->latestEnrollment;

        return view('student.document-requests.index', compact('requests', 'enrollment'));
    }

    public function store(Request $request)
    {
        $student = Auth::guard('student')->user();

        $data = $request->validate([
            'document_type'    => 'required|in:Form 137,Good Moral Certificate,Enrollment Certificate,Diploma,Transcript of Records,Other',
            'other_document'   => 'required_if:document_type,Other|nullable|string|max:200',
            'purpose'          => 'required|string|max:500',
            'copies'           => 'required|integer|min:1|max:10',
        ]);

        $enrollment = $student->latestEnrollment;

        $docReq = DocumentRequest::create([
            'request_no'     => DocumentRequest::generateRequestNo(),
            'student_id'     => $student->id,
            'enrollment_id'  => $enrollment?->id,
            'document_type'  => $data['document_type'],
            'other_document' => $data['other_document'] ?? null,
            'purpose'        => $data['purpose'],
            'copies'         => $data['copies'],
            'status'         => 'pending',
        ]);

        ActivityLog::recordStudent(
            'document_request.submitted',
            "Submitted document request: {$docReq->document_label} ({$docReq->request_no})",
            $docReq
        );

        return back()->with('success', "Document request submitted! Reference: {$docReq->request_no}");
    }
}
