<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('admin')->latest()->paginate(20);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'audience'   => 'required|in:all,jhs,shs',
            'priority'   => 'required|in:normal,important,urgent',
            'expires_at' => 'nullable|date|after:now',
            'publish'    => 'nullable|boolean',
        ]);

        $publish = $request->boolean('publish');

        $ann = Announcement::create([
            'admin_id'     => Auth::guard('admin')->id(),
            'title'        => $data['title'],
            'body'         => $data['body'],
            'audience'     => $data['audience'],
            'priority'     => $data['priority'],
            'is_published' => $publish,
            'published_at' => $publish ? now() : null,
            'expires_at'   => $data['expires_at'] ?? null,
        ]);

        ActivityLog::recordAdmin('announcement.created', "Posted announcement: \"{$ann->title}\"", $ann);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement ' . ($publish ? 'published' : 'saved as draft') . ' successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'audience'   => 'required|in:all,jhs,shs',
            'priority'   => 'required|in:normal,important,urgent',
            'expires_at' => 'nullable|date',
            'publish'    => 'nullable|boolean',
        ]);

        $publish = $request->boolean('publish');

        $announcement->update([
            'title'        => $data['title'],
            'body'         => $data['body'],
            'audience'     => $data['audience'],
            'priority'     => $data['priority'],
            'is_published' => $publish,
            'published_at' => $publish && !$announcement->published_at ? now() : $announcement->published_at,
            'expires_at'   => $data['expires_at'] ?? null,
        ]);

        ActivityLog::recordAdmin('announcement.updated', "Updated announcement: \"{$announcement->title}\"", $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();
        ActivityLog::recordAdmin('announcement.deleted', "Deleted announcement: \"{$title}\"");
        return back()->with('success', 'Announcement deleted.');
    }
}
