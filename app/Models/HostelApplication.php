<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HostelApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'status',
        'admin_notes',
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
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;
        
        return "LH{$year}{$month}" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the admin who reviewed this application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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
}
