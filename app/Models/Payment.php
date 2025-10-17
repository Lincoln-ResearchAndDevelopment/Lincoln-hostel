<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'payment_method',
        'receipt_number',
        'receipt_path',
        'status',
        'notes'
    ];
    

    protected $casts = [
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accessor for formatted date
    public function getPaymentDateFormattedAttribute()
    {
        return $this->payment_date 
            ? $this->payment_date->format('M d, Y') 
            : 'N/A';
    }
    

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getReceiptUrlAttribute()
    {
        if (!$this->receipt_path) {
            return null;
        }
        
        $path = public_path($this->receipt_path);
        return file_exists($path) ? asset($this->receipt_path) : null;
    }

}

