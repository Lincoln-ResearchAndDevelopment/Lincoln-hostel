<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    public function notifications()
    {
        return $this->morphMany(\App\Models\Notification::class, 'notifiable')->latest();
    }

    protected $guard = 'student';

    protected $fillable = [
        'user_id',
        'room_id',
        'admission_number',
        'full_name',
        'email',
        'gender',
        'date_of_birth',
        'nationality',
        'state_of_origin',
        'local_government',
        'profile_photo',
        'department',
        'semester',
        'intake',
        'contact_number',
        'emergency_contact',
        'address',
        
        // Parent/Guardian Information
        'parent_name',
        'parent_relationship',
        'parent_phone',
        'parent_email',
        'parent_address',
        'parent_occupation',
        
        // Medical Information
        'blood_group',
        'genotype',
        'medical_conditions',
        'allergies',
        'medications',
        'has_disability',
        'disability_details',
        
        // Hostel Fee Information
        'hostel_fee_amount',
        'hostel_fee_paid',
        'hostel_fee_status',
        'payment_due_date',
        
        // Other
        'application_id',
        'notification_preferences',
        'check_in_date',
        'expected_check_out_date',
        'status',
        'password',
        'remember_token',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'expected_check_out_date' => 'datetime',
        'date_of_birth' => 'date',
        'payment_due_date' => 'date',
        'has_disability' => 'boolean',
        'hostel_fee_amount' => 'decimal:2',
        'hostel_fee_paid' => 'decimal:2',
        'notification_preferences' => 'array',
        'last_login_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function application()
    {
        return $this->belongsTo(HostelApplication::class, 'application_id');
    }

    public function hostelApplication()
    {
        return $this->belongsTo(HostelApplication::class, 'application_id');
    }

    // ==================== HOSTEL & ROOM INFO ====================

    /**
     * Get the hostel the student is assigned to
     */
    public function getHostelAttribute()
    {
        return $this->room ? $this->room->hostel : null;
    }

    /**
     * Get roommates (other students in the same room)
     */
    public function getRoommatesAttribute()
    {
        if (!$this->room) {
            return collect();
        }
        return $this->room->students->where('id', '!=', $this->id);
    }

    /**
     * Check if student has a room assigned
     */
    public function hasRoom()
    {
        return !is_null($this->room_id);
    }

    /**
     * Get bed/space number in room
     */
    public function getBedNumberAttribute()
    {
        if (!$this->room) {
            return null;
        }
        
        $roomStudents = $this->room->students()->orderBy('check_in_date')->get();
        $position = $roomStudents->search(function($student) {
            return $student->id === $this->id;
        });
        
        return $position !== false ? $position + 1 : null;
    }

    // ==================== PAYMENT & FEE INFO ====================

    /**
     * Get outstanding balance
     */
    public function getOutstandingBalanceAttribute()
    {
        return max(0, $this->hostel_fee_amount - $this->hostel_fee_paid);
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->hostel_fee_amount <= 0) {
            return 100;
        }
        return min(100, round(($this->hostel_fee_paid / $this->hostel_fee_amount) * 100, 2));
    }

    /**
     * Check if payment is overdue
     */
    public function isPaymentOverdue()
    {
        if (!$this->payment_due_date) {
            return false;
        }
        return $this->payment_due_date->isPast() && $this->hostel_fee_status !== 'paid';
    }

    /**
     * Get total amount paid (from payments table)
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * Get pending payments count
     */
    public function getPendingPaymentsCountAttribute()
    {
        return $this->payments()->where('status', 'pending')->count();
    }

    // ==================== COMPLAINTS & REQUESTS ====================

    /**
     * Get active complaints count
     */
    public function getActiveComplaintsCountAttribute()
    {
        return $this->complaints()->whereIn('status', ['submitted', 'in progress'])->count();
    }

    /**
     * Get pending leave requests count
     */
    public function getPendingLeaveRequestsCountAttribute()
    {
        return $this->leaveRequests()->where('status', 'pending')->count();
    }

    /**
     * Check if student is currently on leave
     */
    public function isOnLeave()
    {
        return $this->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }

    /**
     * Get current leave request if on leave
     */
    public function getCurrentLeaveAttribute()
    {
        return $this->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    // ==================== ATTENDANCE ====================

    /**
     * Get last check-in record
     */
    public function getLastCheckInAttribute()
    {
        return $this->attendanceRecords()
            ->where('type', 'check_in')
            ->latest('recorded_at')
            ->first();
    }

    /**
     * Get last check-out record
     */
    public function getLastCheckOutAttribute()
    {
        return $this->attendanceRecords()
            ->where('type', 'check_out')
            ->latest('recorded_at')
            ->first();
    }

    /**
     * Check if student is currently in hostel
     */
    public function isInHostel()
    {
        $lastCheckIn = $this->last_check_in;
        $lastCheckOut = $this->last_check_out;
        
        if (!$lastCheckIn) {
            return false;
        }
        
        if (!$lastCheckOut) {
            return true;
        }
        
        return $lastCheckIn->recorded_at > $lastCheckOut->recorded_at;
    }

    /**
     * Get attendance for a specific date range
     */
    public function getAttendanceForPeriod($startDate, $endDate)
    {
        return $this->attendanceRecords()
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    // ==================== FORMATTED ATTRIBUTES ====================

    /**
     * Get formatted check-in date
     */
    public function getFormattedCheckInDateAttribute()
    {
        return $this->check_in_date ? $this->check_in_date->format('M d, Y') : 'N/A';
    }

    /**
     * Get formatted check-out date
     */
    public function getFormattedCheckOutDateAttribute()
    {
        return $this->expected_check_out_date ? $this->expected_check_out_date->format('M d, Y') : 'N/A';
    }

    /**
     * Get formatted date of birth
     */
    public function getFormattedDateOfBirthAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->format('M d, Y') : 'N/A';
    }

    /**
     * Get age
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get first name
     */
    public function getFirstNameAttribute()
    {
        $parts = explode(' ', $this->full_name);
        return $parts[0] ?? $this->full_name;
    }

    /**
     * Get last name
     */
    public function getLastNameAttribute()
    {
        $parts = explode(' ', $this->full_name);
        return count($parts) > 1 ? end($parts) : '';
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        // Return default avatar based on gender
        $gender = strtolower($this->gender ?? 'male');
        return asset("assets/img/default-avatar-{$gender}.png");
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-danger">Inactive</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get fee status badge
     */
    public function getFeeStatusBadgeAttribute()
    {
        $badges = [
            'paid' => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning">Partial</span>',
            'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
        ];
        return $badges[$this->hostel_fee_status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // ==================== NOTIFICATION PREFERENCES ====================

    /**
     * Get notification preference
     */
    public function getNotificationPreference($key, $default = true)
    {
        $preferences = $this->notification_preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    /**
     * Set notification preference
     */
    public function setNotificationPreference($key, $value)
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$key] = $value;
        $this->notification_preferences = $preferences;
        $this->save();
    }

    // ==================== AUTH ====================

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'admission_number';
    }

    /**
     * Update last login info
     */
    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    // ==================== SCOPES ====================

    /**
     * Scope for active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for students with rooms
     */
    public function scopeWithRoom($query)
    {
        return $query->whereNotNull('room_id');
    }

    /**
     * Scope for students without rooms
     */
    public function scopeWithoutRoom($query)
    {
        return $query->whereNull('room_id');
    }

    /**
     * Scope for students with unpaid fees
     */
    public function scopeWithUnpaidFees($query)
    {
        return $query->where('hostel_fee_status', '!=', 'paid');
    }

    /**
     * Scope for students by hostel
     */
    public function scopeInHostel($query, $hostelId)
    {
        return $query->whereHas('room', function($q) use ($hostelId) {
            $q->where('hostel_id', $hostelId);
        });
    }
}