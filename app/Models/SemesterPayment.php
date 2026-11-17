<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;

class SemesterPayment extends Model
{
    use BelongsToCampus;

    protected $fillable = [
        'student_id', 'semester_number', 'service_charge_receipt', 'hostel_fee_receipt',
        'status', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
