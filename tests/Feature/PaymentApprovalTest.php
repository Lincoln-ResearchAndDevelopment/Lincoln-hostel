<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Hostel $hostel;
    protected Room $room;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create hostel
        $this->hostel = Hostel::create([
            'name' => 'Test Hostel',
            'code' => 'TH1',
            'type' => 'male',
            'address' => 'Test Address',
            'description' => 'Test hostel',
            'status' => 'active',
        ]);

        // Create room with capacity 1
        $this->room = Room::create([
            'hostel_id' => $this->hostel->id,
            'room_number' => '101',
            'room_type' => 'single',
            'capacity' => 1,
            'occupied' => 0,
            'floor_number' => 1,
            'price_per_semester' => 85000,
            'price_per_year' => 250000,
            'status' => 'available',
        ]);

        // Create student
        $this->student = Student::create([
            'user_id' => $this->admin->id,
            'admission_number' => 'STU001',
            'full_name' => 'Test Student',
            'email' => 'student@test.com',
            'gender' => 'male',
            'department' => 'Computer Science',
            'semester' => 1,
            'intake' => 'March 2025',
            'contact_number' => '08012345678',
            'emergency_contact' => '08087654321',
            'address' => 'Test Address',
            'check_in_date' => now(),
            'expected_check_out_date' => now()->addMonths(6),
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function approving_payment_assigns_room_when_capacity_available()
    {
        $payment = Payment::create([
            'student_id' => $this->student->id,
            'room_id' => $this->room->id,
            'payment_plan' => 'semester',
            'amount' => 85000,
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'receipt_number' => 'RCP-001',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->post(route('payments.approve', $payment))
            ->assertRedirect()
            ->assertSessionHas('success');

        $payment->refresh();
        $this->student->refresh();
        $this->room->refresh();

        $this->assertEquals('completed', $payment->status);
        $this->assertEquals($this->room->id, $this->student->room_id);
        $this->assertEquals(1, $this->room->occupied);
    }

    /** @test */
    public function approving_payment_fails_when_room_is_full()
    {
        // Fill the room
        $this->room->update(['occupied' => 1, 'status' => 'full']);

        $payment = Payment::create([
            'student_id' => $this->student->id,
            'room_id' => $this->room->id,
            'payment_plan' => 'semester',
            'amount' => 85000,
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'receipt_number' => 'RCP-002',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->post(route('payments.approve', $payment))
            ->assertRedirect()
            ->assertSessionHas('error');

        $payment->refresh();
        $this->assertNotEquals('completed', $payment->status,
            'Payment should NOT be completed when room is full');
    }

    /** @test */
    public function student_cannot_be_double_assigned_to_rooms()
    {
        // Student already has a room
        $this->student->update(['room_id' => 999]);

        $payment = Payment::create([
            'student_id' => $this->student->id,
            'room_id' => $this->room->id,
            'payment_plan' => 'semester',
            'amount' => 85000,
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'receipt_number' => 'RCP-003',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->post(route('payments.approve', $payment))
            ->assertRedirect()
            ->assertSessionHas('error');

        $payment->refresh();
        $this->assertNotEquals('completed', $payment->status);
    }
}
