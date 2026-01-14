<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'emergency_contact',
        'destination',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get duration in days
     */
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Get type badge HTML
     */
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'medical' => '<span class="badge bg-info">Medical</span>',
            'home' => '<span class="badge bg-success">Home</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->type) . '</span>',
        };
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
