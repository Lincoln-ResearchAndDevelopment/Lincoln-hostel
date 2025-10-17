<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemAlert;

class SystemAlertController extends Controller
{
    public function index()
    {
        $alerts = SystemAlert::latest()->get();

        // Mark all unread system alerts as read when viewed
        SystemAlert::where('is_read', false)->update(['is_read' => true]);

        return view('system_alerts.index', compact('alerts'));
    }
}
