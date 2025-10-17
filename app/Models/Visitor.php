<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'visitor_name',
        'id_number',
        'purpose',
        'check_in_time',
        'check_out_time'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accessor methods
    public function getCheckInTimeFormattedAttribute()
    {
        return $this->check_in_time 
            ? $this->check_in_time->format('M d, Y h:i A') 
            : 'Not checked in';
    }

    public function getCheckOutTimeFormattedAttribute()
    {
        return $this->check_out_time 
            ? $this->check_out_time->format('M d, Y h:i A') 
            : 'Not checked out';
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
