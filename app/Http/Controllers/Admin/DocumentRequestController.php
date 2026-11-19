<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['student', 'handledBy'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $requests = $query->paginate(25)->withQueryString();

        $stats = [
            'pending'    => DocumentRequest::where('status', 'pending')->count(),
            'processing' => DocumentRequest::where('status', 'processing')->count(),
            'ready'      => DocumentRequest::where('status', 'ready')->count(),
            'released'   => DocumentRequest::where('status', 'released')->count(),
        ];

        return view('admin.document-requests.index', compact('requests', 'stats'));
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $data = $request->validate([
            'status'        => 'required|in:pending,processing,ready,released,cancelled',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $updates = [
            'status'        => $data['status'],
            'admin_remarks' => $data['admin_remarks'] ?? $documentRequest->admin_remarks,
            'handled_by'    => Auth::guard('admin')->id(),
        ];

        if ($data['status'] === 'ready' && !$documentRequest->ready_at) {
            $updates['ready_at'] = now();
        }
        if ($data['status'] === 'released' && !$documentRequest->released_at) {
            $updates['released_at'] = now();
        }

        $documentRequest->update($updates);

        ActivityLog::recordAdmin(
            'document_request.status_changed',
            "Updated document request {$documentRequest->request_no} status to {$data['status']}",
            $documentRequest
        );

        return back()->with('success', "Request status updated to \"{$data['status']}\".");
    }
}
