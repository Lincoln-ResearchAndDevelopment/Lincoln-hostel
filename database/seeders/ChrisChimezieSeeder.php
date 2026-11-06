<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostelApplication;
use App\Models\Program;
use App\Models\Intake;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ChrisChimezieSeeder extends Seeder
{
    public function run(): void
    {
        $campusId = \App\Models\Campus::orderBy('id')->value('id');

        // 1. Seed Programs if empty
        if (Program::withoutGlobalScopes()->count() === 0) {
            $depts = ['Computer Science', 'Nursing', 'Business Administration', 'Accounting', 'Public Health'];
            foreach ($depts as $index => $name) {
                Program::create([
                    'campus_id' => $campusId,
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => $index
                ]);
            }
        }

        // 2. Seed Intakes if empty
        if (Intake::withoutGlobalScopes()->count() === 0) {
            $intakes = ['September 2026', 'January 2027', 'May 2027'];
            foreach ($intakes as $index => $name) {
                Intake::create([
                    'campus_id' => $campusId,
                    'name' => $name,
                    'start_date' => now()->addMonths($index * 4),
                    'end_date' => now()->addMonths(($index + 1) * 4),
                    'is_active' => true,
                    'sort_order' => $index
                ]);
            }
        }

        // 3. Create a Pending Application for Chris Chimezie
        $program = Program::withoutGlobalScopes()->first();
        $intake = Intake::withoutGlobalScopes()->first();

        HostelApplication::updateOrCreate(
            ['email' => 'tchimezie475@gmail.com'],
            [
                'status' => 'pending',
                'campus_id' => \App\Models\Campus::orderBy('id')->value('id'),
                'academic_year' => '2026/2027',
                'amount_paid' => '150000',
                'full_name' => 'Chris Chimezie',
                'student_id' => 'STU-CHRIS-001',
                'intake' => $intake->name,
                'program' => $program->name,
                'department' => 'Computer Science',
                'gender' => 'male',
                'date_of_birth' => '2000-01-01',
                'phone_number' => '08012345678',
                'home_address' => '123 Test Street, Lagos',
                'nationality' => 'Nigerian',
                'state_of_origin' => 'Anambra',
                'local_government' => 'Idemili North',
                'parent_full_name' => 'John Chimezie',
                'parent_relationship' => 'Father',
                'parent_phone' => '08087654321',
                'parent_email' => 'john.chimezie@example.com',
                'parent_address' => '123 Test Street, Lagos',
                'parent_occupation' => 'Business Man',
                'emergency_contact_name' => 'Mary Chimezie',
                'emergency_contact_phone' => '08099887766',
                'emergency_contact_address' => '123 Test Street, Lagos',
                'smoking_status' => 'non-smoker',
                'passport_photo' => 'applications/passport/dummy.jpg',
                'applicationform_receipt' => 'applications/receipts/dummy.pdf',
                'hostelfee_receipt' => 'applications/receipts/dummy.pdf',
                'declaration_name' => 'Chris Chimezie',
                'applicant_signature' => 'Chris Chimezie',
                'applicant_date' => now(),
                'guardian_signature' => 'John Chimezie',
                'guardian_date' => now(),
            ]
        );

        $this->command->info('Chris Chimezie pending application seeded successfully.');
    }
}
