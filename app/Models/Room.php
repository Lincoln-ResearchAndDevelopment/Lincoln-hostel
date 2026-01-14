<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_number',
        'room_type',
        'price_per_semester',
        'price_per_year',
        'floor_number',
        'capacity',
        'status',
        'description',
        'gender_type',
        'facilities',
        'images',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'price_per_semester' => 'decimal:2',
        'price_per_year' => 'decimal:2',
    ];

    /**
     * Get the hostel this room belongs to
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

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

    /**
     * Get formatted price per semester
     */
    public function getFormattedPricePerSemesterAttribute()
    {
        return number_format($this->price_per_semester, 2);
    }

    /**
     * Get formatted price per year
     */
    public function getFormattedPricePerYearAttribute()
    {
        return number_format($this->price_per_year, 2);
    }

    /**
     * Check if room is available
     */
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available' && $this->occupied < $this->capacity;
    }

    /**
     * Get room type display name
     */
    public function getRoomTypeDisplayAttribute()
    {
        $types = [
            'single' => 'Single Room',
            'double' => 'Double Room',
            'triple' => 'Triple Room',
            'quad' => 'Quad Room',
            'dormitory' => 'Dormitory',
        ];

        return $types[$this->room_type] ?? ucfirst($this->room_type);
    }
}
