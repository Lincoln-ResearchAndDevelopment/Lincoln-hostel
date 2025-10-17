<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max file size
        ]);

        $announcementData = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Handle file upload if present
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $file = $request->file('attachment');
            $path = $file->store('announcements', 'public');
            
            $announcementData['attachment'] = $path;
            $announcementData['attachment_original_name'] = $file->getClientOriginalName();
            $announcementData['attachment_type'] = $file->getMimeType();
        }

        Announcement::create($announcementData);

        return redirect()->back()->with('success', 'Announcement sent!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max file size
        ]);

        $announcementData = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Handle file upload if present
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            // Delete old file if exists
            if ($announcement->attachment) {
                Storage::disk('public')->delete($announcement->attachment);
            }
            
            $file = $request->file('attachment');
            $path = $file->store('announcements', 'public');
            
            $announcementData['attachment'] = $path;
            $announcementData['attachment_original_name'] = $file->getClientOriginalName();
            $announcementData['attachment_type'] = $file->getMimeType();
        }

        // Handle remove attachment checkbox
        if ($request->has('remove_attachment') && $announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
            $announcementData['attachment'] = null;
            $announcementData['attachment_original_name'] = null;
            $announcementData['attachment_type'] = null;
        }

        $announcement->update($announcementData);

        return redirect()->back()->with('success', 'Announcement Edited Successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        // Delete attachment if exists
        if ($announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
        }
        
        $announcement->delete();
        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    public function downloadAttachment(Announcement $announcement)
    {
        if (!$announcement->attachment) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $announcement->attachment,
            $announcement->attachment_original_name
        );
    }
}