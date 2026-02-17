<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if (isset($notification->data['payment_id'])) {
            return redirect()->route('payments.show', $notification->data['payment_id']);
        }

        return back()->with('success', 'Notification marked as read');
    }

    public function markAllRead()
    {
        Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }
}
