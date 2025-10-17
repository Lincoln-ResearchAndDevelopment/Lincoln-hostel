<?php
// app/Models/Staff.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact',
        'staff_gender',
        'assigned_hostel_gender'
    ];

    protected $casts = [
        'staff_gender' => 'string',
        'assigned_hostel_gender' => 'string'
    ];
}