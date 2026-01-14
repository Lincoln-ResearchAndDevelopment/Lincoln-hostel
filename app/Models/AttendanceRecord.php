<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'recorded_at',
        'recorded_by',
        'notes',
        'location',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the student this record belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope for check-ins
     */
    public function scopeCheckIns($query)
    {
        return $query->where('type', 'check_in');
    }

    /**
     * Scope for check-outs
     */
    public function scopeCheckOuts($query)
    {
        return $query->where('type', 'check_out');
    }

    /**
     * Scope for today's records
     */
    public function scopeToday($query)
    {
        return $query->whereDate('recorded_at', Carbon::today());
    }

    /**
     * Scope for this week's records
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('recorded_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope for this month's records
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('recorded_at', Carbon::now()->month)
                     ->whereYear('recorded_at', Carbon::now()->year);
    }

    /**
     * Get formatted recorded time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->recorded_at->format('M d, Y h:i A');
    }

    /**
     * Get type badge
     */
    public function getTypeBadgeAttribute()
    {
        if ($this->type === 'check_in') {
            return '<span class="badge bg-success"><i class="fas fa-sign-in-alt me-1"></i>Check In</span>';
        }
        return '<span class="badge bg-warning"><i class="fas fa-sign-out-alt me-1"></i>Check Out</span>';
    }
}