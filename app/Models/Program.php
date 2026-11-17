<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use BelongsToCampus;

    protected $fillable = ['name', 'is_active', 'sort_order', 'is_health'];

    protected $casts = ['is_active' => 'boolean', 'is_health' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
