<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'description',
        'status',
        'image_path',
    ];

    /**
     * Get all rooms in this hostel
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get all students in this hostel through rooms
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, Room::class);
    }

    /**
     * Get total number of rooms in this hostel
     */
    public function getTotalRoomsAttribute()
    {
        return $this->rooms()->count();
    }

    /**
     * Get total capacity of this hostel
     */
    public function getTotalCapacityAttribute()
    {
        return $this->rooms()->sum('capacity');
    }

    /**
     * Get currently occupied spaces
     */
    public function getOccupiedSpacesAttribute()
    {
        return $this->students()->where('students.status', 'active')->count();
    }

    /**
     * Get available spaces
     */
    public function getAvailableSpacesAttribute()
    {
        return $this->total_capacity - $this->occupied_spaces;
    }

    /**
     * Get number of available rooms
     */
    public function getAvailableRoomsAttribute()
    {
        return $this->rooms()->where('status', 'available')->whereRaw('occupied < capacity')->count();
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyRateAttribute()
    {
        if ($this->total_capacity == 0) {
            return 0;
        }
        return round(($this->occupied_spaces / $this->total_capacity) * 100, 2);
    }

    /**
     * Get minimum room price for this hostel
     */
    public function getMinPriceAttribute()
    {
        return $this->rooms()->where('status', 'available')->min('price_per_semester');
    }

    /**
     * Get maximum room price for this hostel
     */
    public function getMaxPriceAttribute()
    {
        return $this->rooms()->where('status', 'available')->max('price_per_semester');
    }

    /**
     * Scope for active hostels
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for hostels by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get formatted status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'maintenance' => '<span class="badge bg-warning">Maintenance</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get formatted type badge
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'male' => '<span class="badge bg-primary"><i class="fas fa-mars me-1"></i>Male</span>',
            'female' => '<span class="badge bg-danger"><i class="fas fa-venus me-1"></i>Female</span>',
            'mixed' => '<span class="badge bg-info"><i class="fas fa-venus-mars me-1"></i>Mixed</span>',
        ];

        return $badges[$this->type] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
