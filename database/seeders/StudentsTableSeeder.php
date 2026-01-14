<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test student account for dashboard testing
        Student::updateOrCreate(
            ['admission_number' => 'STU1001'],
            [
                'full_name' => 'Test Student',
                'admission_number' => 'STU1001',
                'gender' => 'Other',
                'department' => 'Testing',
                'semester' => '1',
                'intake' => '2025',
                'contact_number' => '0000000000',
                'address' => 'Test Address',
                'status' => 'active',
                'password' => Hash::make('student12345'),
            ]
        );

        $this->command->info('Test student created: admission_number=STU1001 password=student12345');
    }
}
