<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campuses';

    protected $fillable = [
        'name',
        'code',
        'address',
        'bank_name',
        'account_number',
        'account_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hostels()
    {
        return $this->hasMany(Hostel::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where(function ($q) {
            $q->where('is_admin', true)->orWhere('role', 'admin');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
