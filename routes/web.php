<?php

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentsAuthController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentComplaintController;
use App\Http\Controllers\HostelApplicationController;
use App\Http\Controllers\SuperAdminController;
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
        'rooms'      => RoomController::class,
        'students'   => StudentController::class,
        'payments'   => PaymentController::class,
        'complaints' => ComplaintController::class,
        'visitors'   => VisitorController::class,
    ]);

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

    // Admin session status check route
    Route::get('/check-admin-session', function () {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return response()->json(['status' => 'ok']);
    });
});

/*
|--------------------------------------------------------------------------
| Student Login Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware('guest:student')->group(function () {
    Route::get('/login', [StudentsAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentsAuthController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Student-only Routes (with StudentAuth Middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware('student.auth')->group(function () {
    Route::get('/dashboard', [StudentsDashboardController::class, 'index'])->name('dashboard');
    Route::post('/payments', [StudentPaymentController::class, 'store'])->name('payments.store');
    Route::post('/complaints', [StudentComplaintController::class, 'store'])->name('complaints.store');

    // Student session status check route
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
