<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Visitor;
use App\Models\Complaint;
use App\Models\SystemAlert;
use App\Models\Announcement;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $unreadComplaints = Complaint::where('is_read', false)->count();
            $unreadPayments = Payment::where('is_read', false)->count();
            $unreadVisitors = Visitor::where('is_read', false)->count();
            $unreadSystemAlerts = SystemAlert::where('is_read', false)->count();

            $unreadAnnouncements = 0;
            $latestAnnouncements = collect();

            // Only fetch announcements for authenticated students
            if (Auth::check() && Auth::user()->role === 'student') {
                // No is_read column, so just count all announcements or set to 0
                $unreadAnnouncements = Announcement::count(); // or set to 0 if preferred
                $latestAnnouncements = Announcement::orderBy('created_at', 'desc')->take(5)->get();
            }


            $view->with(compact(
                'unreadComplaints',
                'unreadPayments',
                'unreadVisitors',
                'unreadSystemAlerts',
                'unreadAnnouncements',
                'latestAnnouncements'
            ));
        });
    }
}
