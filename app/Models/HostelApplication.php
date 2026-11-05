<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HostelApplication extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToCampus;

    protected $fillable = [
        'application_number',
        'status',
        'admin_notes',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
        
        // Academic Information
        'academic_year',
        'amount_paid',
        
        // Student Information
        'full_name',
        'student_id',
        'intake',
        'program',
        'department',
        'gender',
        'date_of_birth',
        'phone_number',
        'email',
        'home_address',
        'nationality',
        'state_of_origin',
        'local_government',
        
        // Parent/Guardian Information
        'parent_full_name',
        'parent_relationship',
        'parent_phone',
        'parent_email',
        'parent_address',
        'parent_occupation',
        'parent_workplace',
        
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'emergency_contact_address',
        
        // Medical Information
        'medical_conditions',
        'allergies',
        'medications',
        'blood_group',
        'genotype',
        'dietary_requirements',
        'has_disability',
        'disability_details',
        'smoking_status',
        'vaccination_status',
        'insurance_info',
        'preferred_hospital',
        'physical_restrictions',
        
        // Accommodation Preferences
        'preferred_hostel_id',
        'preferred_room_capacity',
        'preferred_hostel_type',
        'preferred_room_type',
        'special_accommodation_needs',
        
        // Documents
        'passport_photo',
        'applicationform_receipt',
        'hostelfee_receipt',
        'medical_report',
        'birth_certificate',
        'admission_letter',
        
        // Declaration and Signatures
        'declaration_name',
        'applicant_signature',
        'applicant_date',
        'guardian_signature',
        'guardian_date',
        
        // Additional Information
        'previous_hostel_experience',
        'why_choose_hostel',
        'references',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'applicant_date' => 'date',
        'guardian_date' => 'date',
        'reviewed_at' => 'datetime',
        'has_disability' => 'boolean',
        'references' => 'array',
    ];

    /**
     * "amount_paid" holds the plan the student paid for ("semester" / "full_year"),
     * or an older numeric amount. Return something safe to print either way.
     */
    public function getAmountPaidDisplayAttribute(): string
    {
        $value = $this->amount_paid;

        if (is_numeric($value)) {
            return '₦' . number_format((float) $value, 2);
        }

        return match (strtolower((string) $value)) {
            'semester' => 'Per semester',
            'full_year' => 'Full year',
            default => (string) $value,
        };
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            if (empty($application->application_number)) {
                $application->application_number = self::generateApplicationNumber();
            }
        });
    }

    /**
     * Generate unique application number
     */
    public static function generateApplicationNumber()
    {
        $year  = date('Y'); // e.g. 2026
        $month = date('m'); // e.g. 03, 04

        // Count applications in the current month to get the sequence
        $count = self::whereYear('created_at', $year)
                      ->whereMonth('created_at', $month)
                      ->count() + 1;

        // Format: LH + YYYY + MM + 4-digit sequence → e.g. LH2026050001
        return 'LH' . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the admin who reviewed this application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the student associated with this application
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'admission_number', 'student_id');
    }

    /**
     * Get status badge for display
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'under_review' => '<span class="badge bg-info">Under Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get formatted application date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M d, Y H:i A');
    }

    /**
     * Get formatted review date
     */
    public function getFormattedReviewedAtAttribute()
    {
        return $this->reviewed_at ? $this->reviewed_at->format('M d, Y H:i A') : 'Not reviewed';
    }

    /**
     * Check if application is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the application
     */
    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject the application
     */
    public function reject($adminId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Set application under review
     */
    public function setUnderReview($adminId, $notes = null)
    {
        $this->update([
            'status' => 'under_review',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for recent applications
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    /**
     * The hostel the applicant said they paid for.
     */
    public function preferredHostel()
    {
        return $this->belongsTo(Hostel::class, 'preferred_hostel_id');
    }

    /**
     * What the applicant should have paid for the hostel + room size they chose, for the
     * payment plan they picked (semester or full year). Rooms are priced one by one, so this is
     * a range; min === max when every matching room costs the same. Null when nothing is recorded
     * (older applications) or no matching room exists any more.
     *
     * @return array{min: float, max: float, plan: string}|null
     */
    public function expectedFee(): ?array
    {
        if (!$this->preferred_hostel_id || !$this->preferred_room_capacity) {
            return null;
        }

        $plan = $this->amount_paid === 'full_year' ? 'full_year' : 'semester';
        $column = $plan === 'full_year' ? 'price_per_year' : 'price_per_semester';

        $rooms = Room::withoutGlobalScopes()
            ->where('hostel_id', $this->preferred_hostel_id)
            ->where('capacity', $this->preferred_room_capacity)
            ->where('status', '!=', 'maintenance');

        if (!$rooms->exists()) {
            return null;
        }

        return [
            'min' => (float) $rooms->min($column),
            'max' => (float) $rooms->max($column),
            'plan' => $plan,
        ];
    }
}
