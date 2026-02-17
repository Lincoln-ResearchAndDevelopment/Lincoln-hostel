<?php

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentsAuthController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentComplaintController;
use App\Http\Controllers\HostelApplicationController;
use App\Http\Controllers\StudentsDashboardController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/home')->name('welcome');
Route::view('/home', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/features', 'features')->name('features');
Route::view('/faq', 'faq')->name('faq');
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Hostel Application Form Routes
|--------------------------------------------------------------------------
*/
Route::get('/hostel/apply', [HostelApplicationController::class, 'create'])->name('apply');
Route::post('/hostel/apply', [HostelApplicationController::class, 'store'])->name('hostel.apply');
Route::get('/application/{applicationNumber}', [HostelApplicationController::class, 'show'])->name('application.show');
Route::get('/check-application', function() {
    return view('check-application');
})->name('check.application');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Admin)
|--------------------------------------------------------------------------
*/
Auth::routes([
    'register' => true,
    'verify'   => false,
]);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin-only Routes (with AdminMiddleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->group(function () {
    // Redirect /admin to /dashboard for better UX
    Route::get('/admin', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resources([
        'hostels'    => HostelController::class,
        'rooms'      => RoomController::class,
        'students'   => StudentController::class,
        'payments'   => PaymentController::class,
        'complaints' => ComplaintController::class,
        'visitors'   => VisitorController::class,
    ]);

    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/students-search-applications', [StudentController::class, 'searchApplications'])->name('students.search-applications');

    // Additional hostel routes
    Route::get('/hostels/{hostel}/rooms', [HostelController::class, 'rooms'])->name('hostels.rooms');

    Route::post('/visitors/{visitor}/checkout', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::get('/students/{student}/profile', [StudentController::class, 'profile'])->name('students.profile');

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::post('/update', [ProfileController::class, 'update'])->name('update');

    // Password change routes
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('password.form');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('password.update');
});


Route::middleware(['auth'])->group(function () {
    // Staff Management Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
});


    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('/announcements/{announcement}/download', [AnnouncementController::class, 'downloadAttachment'])->name('announcements.download');

    // Hostel Applications Management
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [HostelApplicationController::class, 'adminIndex'])->name('index');
        Route::get('/{application}', [HostelApplicationController::class, 'adminShow'])->name('show');
        Route::patch('/{application}/status', [HostelApplicationController::class, 'updateStatus'])->name('update-status');
    });

    // Admin session status check route
    Route::get('/check-admin-session', function () {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return response()->json(['status' => 'ok']);
    });

    // Admin Management Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Leave Requests Management
        Route::get('/leave', [App\Http\Controllers\Admin\LeaveRequestController::class, 'index'])->name('leave.index');
        Route::get('/leave/{leaveRequest}', [App\Http\Controllers\Admin\LeaveRequestController::class, 'show'])->name('leave.show');
        Route::post('/leave/{leaveRequest}/approve', [App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('leave.approve');
        Route::post('/leave/{leaveRequest}/reject', [App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('leave.reject');

        // Attendance Management
        Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/create', [App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/student/{student}', [App\Http\Controllers\Admin\AttendanceController::class, 'studentHistory'])->name('attendance.student');

        // Fee Structure Management
        Route::get('/fees', [App\Http\Controllers\Admin\FeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/students', [App\Http\Controllers\Admin\FeeController::class, 'studentFees'])->name('fees.students');
        Route::patch('/fees/room/{room}', [App\Http\Controllers\Admin\FeeController::class, 'updateRoomPrice'])->name('fees.room.update');
        Route::patch('/fees/student/{student}', [App\Http\Controllers\Admin\FeeController::class, 'updateStudentFee'])->name('fees.student.update');

        // Announcements Management
        Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        // Hostel Rules Management
        Route::get('/rules', [App\Http\Controllers\Admin\HostelRuleController::class, 'index'])->name('rules.index');
        Route::get('/rules/create', [App\Http\Controllers\Admin\HostelRuleController::class, 'create'])->name('rules.create');
        Route::post('/rules', [App\Http\Controllers\Admin\HostelRuleController::class, 'store'])->name('rules.store');
        Route::get('/rules/{rule}/edit', [App\Http\Controllers\Admin\HostelRuleController::class, 'edit'])->name('rules.edit');
        Route::put('/rules/{rule}', [App\Http\Controllers\Admin\HostelRuleController::class, 'update'])->name('rules.update');
        Route::delete('/rules/{rule}', [App\Http\Controllers\Admin\HostelRuleController::class, 'destroy'])->name('rules.destroy');
        Route::patch('/rules/{rule}/toggle', [App\Http\Controllers\Admin\HostelRuleController::class, 'toggleStatus'])->name('rules.toggle');

        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/occupancy', [App\Http\Controllers\Admin\ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('/reports/fees', [App\Http\Controllers\Admin\ReportController::class, 'fees'])->name('reports.fees');
        Route::get('/reports/complaints', [App\Http\Controllers\Admin\ReportController::class, 'complaints'])->name('reports.complaints');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/mark-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    });
});

/*
|--------------------------------------------------------------------------
| Student Login Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
// Student login routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentsAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentsAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [StudentsAuthController::class, 'logout'])->name('logout');
});
/*
|--------------------------------------------------------------------------
| Student-only Routes (with StudentAuth Middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware('student.auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentsDashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\StudentProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [\App\Http\Controllers\StudentProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [\App\Http\Controllers\StudentProfileController::class, 'changePasswordForm'])->name('password.change');
    Route::post('/profile/change-password', [\App\Http\Controllers\StudentProfileController::class, 'changePassword'])->name('password.update');

    // Room & Hostel Details
    Route::get('/hostels', [StudentsDashboardController::class, 'hostels'])->name('hostels.index');
    Route::get('/hostels/{hostel}', [StudentsDashboardController::class, 'showHostel'])->name('hostels.show');
    Route::post('/rooms/{room}/book', [StudentsDashboardController::class, 'bookRoom'])->name('rooms.book');
    Route::get('/rooms/{room}/payment', [StudentsDashboardController::class, 'showBookingPayment'])->name('rooms.booking_payment');
    Route::post('/rooms/{room}/submit-payment', [StudentsDashboardController::class, 'submitBookingPayment'])->name('rooms.submit_booking_payment');
    
    Route::get('/room', [StudentsDashboardController::class, 'roomDetails'])->name('room.details');
    Route::get('/hostel/rules', [StudentsDashboardController::class, 'hostelRules'])->name('hostel.rules');

    // Announcements & Notices
    Route::get('/announcements', [StudentsDashboardController::class, 'announcements'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [StudentsDashboardController::class, 'showAnnouncement'])->name('announcements.show');

    // Attendance / In-Out Records
    Route::get('/attendance', [StudentsDashboardController::class, 'attendance'])->name('attendance.index');

    // Fee Details & Payment Status
    Route::get('/fees', [StudentsDashboardController::class, 'feeDetails'])->name('fees.index');

    // Payments
    Route::post('/payments', [StudentPaymentController::class, 'store'])->name('payments.store');

    // Complaints
    Route::get('/complaints', [StudentsDashboardController::class, 'complaints'])->name('complaints.index');
    Route::get('/complaints/{id}', [StudentsDashboardController::class, 'showComplaint'])->name('complaints.show');
    Route::post('/complaints', [StudentComplaintController::class, 'store'])->name('complaints.store');

    // Leave Requests
    Route::get('/leave', [App\Http\Controllers\StudentLeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [App\Http\Controllers\StudentLeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave', [App\Http\Controllers\StudentLeaveController::class, 'store'])->name('leave.store');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\StudentNotificationsController::class, 'index'])->name('notifications');
    Route::get('/notifications/fetch', [\App\Http\Controllers\StudentNotificationsController::class, 'fetchNew'])->name('notifications.fetch');
    Route::post('/notifications', [\App\Http\Controllers\StudentNotificationsController::class, 'update'])->name('notifications.update');
    Route::get('/notifications/{id}/mark-read', [\App\Http\Controllers\StudentNotificationsController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\StudentNotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Application Details (if came from application)
    Route::get('/application', [StudentsDashboardController::class, 'applicationDetails'])->name('application.details');

    // Session Check
    Route::get('/check-session', function () {
        if (!Auth::guard('student')->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return response()->json(['status' => 'ok']);
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->name('superadmin.')->middleware('web')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login', [SuperAdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SuperAdminController::class, 'login'])->name('login.post');
    });

    // Authenticated routes
    Route::middleware('superadmin')->group(function () {
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [SuperAdminController::class, 'profile'])->name('show');
            Route::get('/edit', [SuperAdminController::class, 'editProfile'])->name('edit');
            Route::post('/update', [SuperAdminController::class, 'updateProfile'])->name('update');
            Route::get('/change-password', [SuperAdminController::class, 'changePasswordForm'])->name('password.form');
            Route::post('/change-password', [SuperAdminController::class, 'changePassword'])->name('password.update');
        });

        // Admin Management routes
        Route::prefix('admin-management')->name('admin-management.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('/{adminUser}', [AdminManagementController::class, 'show'])->name('show');
            Route::get('/{adminUser}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{adminUser}', [AdminManagementController::class, 'update'])->name('update');
            Route::delete('/{adminUser}', [AdminManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{adminUser}/activate', [AdminManagementController::class, 'activate'])->name('activate');
            Route::get('/stats/ajax', [AdminManagementController::class, 'getStats'])->name('stats.ajax');
        });
    });
});
