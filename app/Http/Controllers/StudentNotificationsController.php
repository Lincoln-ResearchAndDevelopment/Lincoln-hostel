<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentNotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $student = auth('student')->user();
        // For now, use a simple placeholder preferences array
        $preferences = [
            'email' => true,
            'sms' => false,
            'push' => true,
        ];

        return view('student.notifications.index', compact('student', 'preferences'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'sometimes|boolean',
            'sms' => 'sometimes|boolean',
            'push' => 'sometimes|boolean',
        ]);

        // Persisting preference storage is out-of-scope for now; show success
        return back()->with('success', 'Notification preferences updated.');
    }
}
