<?php

namespace App\View\Composers;

use App\Models\Payment;
use App\Models\Complaint;
use App\Models\LeaveRequest;
use App\Models\HostelApplication;
use Illuminate\View\View;

class AdminSidebarComposer
{
    /**
     * Compose the admin layout sidebar data.
     *
     * Replaces 5+ inline COUNT queries in admin.blade.php with
     * a single View Composer that runs once per request.
     */
    public function compose(View $view): void
    {
        $pendingBookingsCount = Payment::where('status', 'pending')
            ->whereNotNull('room_id')
            ->count();

        $pendingApps = HostelApplication::where('status', 'pending')->count();

        $pendingLeave = LeaveRequest::where('status', 'pending')->count();

        $pendingComplaints = Complaint::whereIn('status', ['submitted', 'in progress'])->count();

        $newComplaints = Complaint::where('status', 'submitted')->count();

        // Combined notification count for header badge
        $notifCount = $pendingApps
            + $pendingLeave
            + $newComplaints
            + $pendingBookingsCount;

        $view->with(compact(
            'pendingBookingsCount',
            'pendingApps',
            'pendingLeave',
            'pendingComplaints',
            'newComplaints',
            'notifCount',
        ));
    }
}
