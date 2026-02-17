<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Student;
use App\Models\Notification;
use App\Mail\AnnouncementNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_audience' => 'required|in:General,Male,Female',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'target_audience' => $request->target_audience,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store('announcements', 'public');
            $data['attachment_original_name'] = $file->getClientOriginalName();
            $data['attachment_mime_type'] = $file->getMimeType();
        }

        $announcement = Announcement::create($data);

        // Notify Students
        try {
            $studentsQuery = Student::where('status', 'active');
            
            if ($request->target_audience === 'Male') {
                $studentsQuery->where('gender', 'Male');
            } elseif ($request->target_audience === 'Female') {
                $studentsQuery->where('gender', 'Female');
            }

            $students = $studentsQuery->get();

            foreach ($students as $student) {
                // Dashboard Notification
                Notification::notifyStudent(
                    $student->id,
                    'announcement',
                    $announcement->title,
                    Str::limit(strip_tags($announcement->description), 100),
                    ['announcement_id' => $announcement->id]
                );

                // Email Notification
                if ($student->email) {
                    Mail::to($student->email)->send(new AnnouncementNotificationMail($announcement));
                }
            }
        } catch (\Exception $e) {
            // Log error but don't stop the announcement from being published
            \Log::error('Failed to notify students of announcement: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Announcement published successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_audience' => 'required|in:General,Male,Female',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'target_audience' => $request->target_audience,
        ];

        if ($request->has('remove_attachment') && $announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
            $data['attachment_path'] = null;
            $data['attachment_original_name'] = null;
            $data['attachment_mime_type'] = null;
        }

        if ($request->hasFile('attachment')) {
            if ($announcement->attachment_path) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store('announcements', 'public');
            $data['attachment_original_name'] = $file->getClientOriginalName();
            $data['attachment_mime_type'] = $file->getMimeType();
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
