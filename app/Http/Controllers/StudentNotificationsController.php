<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('student.auth');
    }

    /**
     * Display all notifications
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        $notifications = $student->notifications()->paginate(15);
        $unreadCount = $student->notifications()->unread()->count();

        return view('student.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $student = Auth::guard('student')->user();
        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification dismissed.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        $student->notifications()->unread()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Fetch latest notifications for polling (JSON)
     */
    public function fetchNew()
    {
        $student = Auth::guard('student')->user();
        $notifications = $student->notifications()
            ->unread()
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $student->notifications()->unread()->count(),
            'totalCount' => $student->notifications()->count()
        ]);
    }

    /**
     * Update notification preferences (placeholder for future)
     */
    public function update(Request $request)
    {
        // Placeholder for notification preferences
        return redirect()->back()->with('success', 'Notification preferences updated.');
    }
}
