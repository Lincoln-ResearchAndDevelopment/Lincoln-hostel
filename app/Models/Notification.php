<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity (student or user)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'leave_approved' => 'fas fa-check-circle text-success',
            'leave_rejected' => 'fas fa-times-circle text-danger',
            'leave_submitted' => 'fas fa-calendar-alt text-info',
            'announcement' => 'fas fa-bullhorn text-warning',
            'payment' => 'fas fa-credit-card text-primary',
            'complaint' => 'fas fa-exclamation-triangle text-warning',
            default => 'fas fa-bell text-secondary',
        };
    }

    /**
     * Create notification for student
     */
    public static function notifyStudent($studentId, $type, $title, $message, $data = [])
    {
        return self::create([
            'notifiable_type' => Student::class,
            'notifiable_id' => $studentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
