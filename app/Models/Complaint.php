<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject',
        'description',
        'status',
        'resolution',
        'resolved_at'
    ];

    protected $dates = [
        'resolved_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
