<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'order',
        'is_active',
        'hostel_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the hostel this rule belongs to
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Scope for active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for rules by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for global rules (applies to all hostels)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('hostel_id');
    }

    /**
     * Scope for hostel-specific rules
     */
    public function scopeForHostel($query, $hostelId)
    {
        return $query->where(function($q) use ($hostelId) {
            $q->whereNull('hostel_id')
              ->orWhere('hostel_id', $hostelId);
        });
    }

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return [
            'general' => 'General Rules',
            'safety' => 'Safety & Security',
            'conduct' => 'Code of Conduct',
            'facilities' => 'Facilities Usage',
            'visitors' => 'Visitor Policy',
            'curfew' => 'Curfew & Timing',
            'cleanliness' => 'Cleanliness & Hygiene',
            'prohibited' => 'Prohibited Items/Activities',
        ];
    }

    /**
     * Get category display name
     */
    public function getCategoryNameAttribute()
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? ucfirst($this->category);
    }
}