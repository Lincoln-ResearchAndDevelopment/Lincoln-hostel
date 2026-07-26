<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function student_creation_generates_random_password_not_hardcoded()
    {
        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'admission_number' => 'STU100',
                'full_name' => 'Jane Doe',
                'email' => 'jane@test.com',
                'gender' => 'female',
                'department' => 'Biology',
                'semester' => 2,
                'intake' => 'March 2025',
                'contact_number' => '08012345678',
                'emergency_contact' => '08087654321',
                'address' => '456 Test Ave',
                'check_in_date' => now()->format('Y-m-d'),
                'expected_check_out_date' => now()->addMonths(6)->format('Y-m-d'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $student = Student::where('admission_number', 'STU100')->first();
        $this->assertNotNull($student, 'Student should be created');

        $user = User::find($student->user_id);
        $this->assertNotNull($user, 'User should be created for student');

        // Verify password is NOT the hardcoded 'welcome123'
        $this->assertFalse(
            password_verify('welcome123', $user->password),
            'Password must NOT be the hardcoded welcome123'
        );

        // Verify password was set (not null/empty)
        $this->assertNotEmpty($user->password, 'Password must be set');
        $this->assertTrue(strlen($user->password) >= 60, 'Password hash must be proper bcrypt output');

        // Verify random password is verifiable
        $this->assertFalse(
            password_verify('student12345', $user->password),
            'Each student must have a unique random password'
        );
    }

    /** @test */
    public function student_requires_valid_intake_value()
    {
        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'admission_number' => 'STU200',
                'full_name' => 'Invalid Intake Student',
                'email' => 'invalid@test.com',
                'gender' => 'male',
                'department' => 'Physics',
                'semester' => 1,
                'intake' => 'Invalid Intake Value',
                'contact_number' => '08012345678',
                'emergency_contact' => '08087654321',
                'address' => '789 Test Ave',
                'check_in_date' => now()->format('Y-m-d'),
                'expected_check_out_date' => now()->addMonths(6)->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('intake');

        $this->assertDatabaseMissing('students', ['admission_number' => 'STU200']);
    }
}
