<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'student';

    protected $fillable = [
        'user_id',
        'room_id',
        'admission_number',
        'full_name',
        'gender',
        'department',
        'semester',
        'intake',
        'contact_number',
        'emergency_contact',
        'address',
        'check_in_date',
        'expected_check_out_date',
        'status',
        'password',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token', // Hide sensitive information
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'expected_check_out_date' => 'datetime',
    ];

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
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'admission_number'; // Use admission_number as the login credential
    }

    public function isAdmin()
    {
        return $this->role === 'admin'; // Adjust 'role' to your actual field name and 'admin' to the appropriate value
    }

}   