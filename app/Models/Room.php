<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 
        'capacity', 
        'status', 
        'description',
        'gender_type'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Computed property to count current students
    public function getOccupiedAttribute()
    {
        return $this->students()->count();
    }

    // Computed property to determine room status
    public function getCurrentStatusAttribute()
    {
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }

        return $this->occupied >= $this->capacity ? 'full' : 'available';
    }
}
