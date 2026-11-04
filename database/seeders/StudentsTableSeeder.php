<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Support\Facades\Hash;

class StudentsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test student account for dashboard testing
        $testStudent = Student::updateOrCreate(
            ['admission_number' => 'STU1001'],
            [
                'full_name' => 'Test Student',
                'campus_id' => \App\Models\Campus::orderBy('id')->value('id'),
                'admission_number' => 'STU1001',
                'gender' => 'Male',
                'department' => 'Testing',
                'semester' => '1',
                'intake' => 'July 2026',
                'contact_number' => '0000000000',
                'emergency_contact' => '0000000001',
                'address' => 'Test Address',
                'status' => 'active',
                'password' => Hash::make('student12345'),
            ]
        );

        // Generate 50 dummy students using factory
        $students = Student::factory()->count(50)->create();
        
        $this->command->info('Generated 50 dummy students.');

        // For each student, generate an application and a payment
        foreach ($students as $student) {
            $application = \App\Models\HostelApplication::factory()->create([
                'student_id' => $student->id,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'phone_number' => $student->contact_number,
                'email' => $student->email,
            ]);

            // Create a matching payment
            \App\Models\Payment::create([
                'student_id' => $student->id,
                'amount' => $application->amount_paid,
                'payment_method' => 'Bank Transfer',
                'receipt_number' => 'TXN' . rand(100000, 999999),
                'receipt_path' => 'receipts/dummy.jpg',
                'status' => 'completed',
                'payment_date' => now(),
                'is_read' => 0,
            ]);

            // Assign a room and bed if available
            $hostel = Hostel::where('type', strtolower($student->gender))->first();
            if ($hostel) {
                $room = $hostel->rooms()->whereRaw('occupied < capacity')->first();
                if ($room) {
                    $bed = $room->beds()->where('is_occupied', false)->first();
                    if ($bed) {
                        $student->update([
                            'room_id' => $room->id,
                            'bed_id' => $bed->id,
                            'hostel_fee_status' => 'paid',
                            'hostel_fee_amount' => $application->amount_paid,
                            'hostel_fee_paid' => $application->amount_paid,
                            'check_in_date' => now(),
                        ]);
                        $bed->update(['is_occupied' => true, 'student_id' => $student->id]);
                        $room->increment('occupied');
                        if ($room->occupied >= $room->capacity) {
                            $room->update(['status' => 'full']);
                        }
                    }
                }
            }
        }

        $this->command->info('Generated 50 hostel applications and payments for the students.');
    }
}
