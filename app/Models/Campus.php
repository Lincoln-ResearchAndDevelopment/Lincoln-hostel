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
        'service_charge_amount',
        'service_charge_amount_year',
        'service_charge_bank_name',
        'service_charge_account_number',
        'service_charge_account_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'service_charge_amount' => 'decimal:2',
        'service_charge_amount_year' => 'decimal:2',
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
