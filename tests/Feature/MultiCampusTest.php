<?php

namespace Tests\Feature;

use App\Models\Bed;
use App\Models\Campus;
use App\Models\Hostel;
use App\Models\HostelApplication;
use App\Models\Room;
use App\Models\Student;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiCampusTest extends TestCase
{
    use RefreshDatabase;

    protected Campus $main;
    protected Campus $north;
    protected User $adminMain;
    protected User $adminNorth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dropLegacyRegNumberOnSqlite();

        // The migration creates the first campus; add a second one.
        $this->main = Campus::orderBy('id')->first();
        $this->north = Campus::create([
            'name' => 'North Campus',
            'code' => 'NORTH',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'Lincoln North',
            'service_charge_amount' => 7500,
            'service_charge_bank_name' => 'Sterling',
            'service_charge_account_number' => '9988776655',
            'service_charge_account_name' => 'Lincoln North Services',
        ]);

        $this->adminMain = $this->makeAdmin('main@test.com', $this->main);
        $this->adminNorth = $this->makeAdmin('north@test.com', $this->north);
    }

    /**
     * The project's own migrations skip some hostel_applications changes on SQLite (the
     * legacy reg_number column is dropped on MySQL only). Apply them here so the tests
     * match production.
     */
    private function dropLegacyRegNumberOnSqlite(): void
    {
        if (DB::getDriverName() === 'sqlite' && Schema::hasColumn('hostel_applications', 'reg_number')) {
            Schema::table('hostel_applications', fn (Blueprint $t) => $t->dropIndex(['reg_number']));
            Schema::table('hostel_applications', fn (Blueprint $t) => $t->dropColumn('reg_number'));
        }

        // Same story: made nullable on MySQL, but the SQLite schema keeps it NOT NULL.
        if (DB::getDriverName() === 'sqlite' && Schema::hasColumn('hostel_applications', 'emergency_contact_relationship')) {
            DB::statement('ALTER TABLE hostel_applications ADD COLUMN ecr_tmp varchar');
            DB::statement('UPDATE hostel_applications SET ecr_tmp = emergency_contact_relationship');
            DB::statement('ALTER TABLE hostel_applications DROP COLUMN emergency_contact_relationship');
            DB::statement('ALTER TABLE hostel_applications RENAME COLUMN ecr_tmp TO emergency_contact_relationship');
        }
    }

    /** Logged in the way the app's session middleware expects (guard + session context). */
    private function asAdmin(User $user)
    {
        return $this->actingAs($user)->withSession(['auth_context_admin' => ['user_id' => $user->id]]);
    }

    private function asMaster(SuperAdmin $master)
    {
        return $this->actingAs($master, 'superadmin')->withSession(['auth_context_superadmin' => ['user_id' => $master->id]]);
    }

    private function asStudent(Student $student)
    {
        return $this->actingAs($student, 'student')->withSession(['auth_context_student' => ['user_id' => $student->id]]);
    }

    private function makeAdmin(string $email, ?Campus $campus): User
    {
        return User::create([
            'name' => $email,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
            'is_active' => true,
            'campus_id' => $campus?->id,
        ]);
    }

    /** Builds hostel -> room -> beds for a campus. */
    private function makeHostel(Campus $campus, string $code, string $type = 'male', int $capacity = 2, int $semester = 80000, int $year = 150000): array
    {
        $hostel = Hostel::create([
            'campus_id' => $campus->id,
            'name' => "{$campus->code} {$code}",
            'code' => $code,
            'type' => $type,
            'status' => 'active',
        ]);
        $room = Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => '101',
            'room_type' => 'double',
            'capacity' => $capacity,
            'occupied' => 0,
            'floor_number' => 1,
            'price_per_semester' => $semester,
            'price_per_year' => $year,
            'status' => 'available',
            'gender_type' => $type,
        ]);
        $beds = [];
        for ($i = 1; $i <= $capacity; $i++) {
            $beds[] = Bed::create(['room_id' => $room->id, 'bed_number' => "B{$i}", 'is_occupied' => false]);
        }

        return [$hostel, $room, $beds];
    }

    private function makeApplication(Campus $campus, string $studentId, string $plan = 'semester'): HostelApplication
    {
        return HostelApplication::factory()->create([
            'campus_id' => $campus->id,
            'student_id' => $studentId,
            'status' => 'pending',
            'gender' => 'male',
            'amount_paid' => $plan,
        ]);
    }

    // ---------------------------------------------------------------- data

    public function test_records_inherit_campus_from_their_parent(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->north, 'N1');

        $this->assertSame($this->north->id, $room->fresh()->campus_id);
        $this->assertSame($this->north->id, $beds[0]->fresh()->campus_id);
    }

    // ---------------------------------------------------------------- public form

    private function applicationPayload(array $overrides = []): array
    {
        Storage::fake('public');

        return array_merge([
            'academic_year' => '2026/2027',
            'amount_paid' => 'semester',
            'full_name' => 'Ada Obi',
            'student_id' => 'MAT-001',
            'intake' => 'July 2026',
            'program' => 'BSc',
            'department' => 'Computer Science',
            'gender' => 'female',
            'date_of_birth' => '2003-01-01',
            'phone_number' => '08012345678',
            'email' => 'ada@example.com',
            'home_address' => '1 Test Street',
            'nationality' => 'Nigerian',
            'state_of_origin' => 'Lagos',
            'local_government' => 'Ikeja',
            'parent_full_name' => 'Parent One',
            'parent_relationship' => 'Mother',
            'parent_phone' => '08011111111',
            'parent_address' => '1 Test Street',
            'parent_occupation' => 'Trader',
            'emergency_contact_name' => 'Uncle',
            'emergency_contact_phone' => '08022222222',
            'emergency_contact_address' => '2 Test Street',
            'smoking_status' => 'non-smoker',
            'passport_photo' => UploadedFile::fake()->image('p.jpg'),
            'applicationform_receipt' => UploadedFile::fake()->image('a.jpg'),
            'hostelfee_receipt' => UploadedFile::fake()->image('h.jpg'),
            'declaration_name' => 'Ada Obi',
            'applicant_signature' => 'Ada Obi',
            'applicant_date' => '2026-11-01',
            'guardian_signature' => 'Parent One',
            'guardian_date' => '2026-11-01',
        ], $overrides);
    }

    public function test_application_form_lists_campuses_and_their_accounts(): void
    {
        $this->get(route('apply'))
            ->assertOk()
            ->assertSee('North Campus')
            ->assertSee('0123456789')
            ->assertSee('1311150112')       // main campus account, now data not markup
            ->assertSee('Lincoln North');
    }

    public function test_application_requires_a_valid_active_campus(): void
    {
        $this->post(route('hostel.apply'), $this->applicationPayload())
            ->assertSessionHasErrors('campus_id');

        $this->north->update(['is_active' => false]);
        $this->post(route('hostel.apply'), $this->applicationPayload(['campus_id' => $this->north->id]))
            ->assertSessionHasErrors('campus_id');

        $this->assertDatabaseCount('hostel_applications', 0);
    }

    public function test_application_is_saved_against_the_chosen_campus_and_only_that_campus_admins_are_emailed(): void
    {
        $this->post(route('hostel.apply'), $this->applicationPayload(['campus_id' => $this->north->id]))
            ->assertSessionHasNoErrors()
            ->assertSessionMissing('error');

        $application = HostelApplication::withoutGlobalScopes()->firstOrFail();
        $this->assertSame($this->north->id, $application->campus_id);

        // Dashboard notification went to the North admin only.
        $this->assertDatabaseHas('notifications', ['notifiable_type' => User::class, 'notifiable_id' => $this->adminNorth->id]);
        $this->assertDatabaseMissing('notifications', ['notifiable_type' => User::class, 'notifiable_id' => $this->adminMain->id]);
    }

    public function test_room_price_list_is_per_campus_and_grouped_by_capacity(): void
    {
        $this->makeHostel($this->main, 'M1', 'male', 2, 80000, 150000);
        $this->makeHostel($this->main, 'M2', 'female', 4, 50000, 90000);
        $this->makeHostel($this->north, 'N1', 'male', 3, 60000, 110000);

        $main = $this->getJson(route('campus.room-prices', $this->main))->assertOk()->json();
        $this->assertSame([2, 4], array_map('intval', array_column($main, 'capacity')));
        $this->assertEquals(80000, $main[0]['semester_min']);

        $north = $this->getJson(route('campus.room-prices', $this->north))->assertOk()->json();
        $this->assertCount(1, $north);
        $this->assertEquals(3, $north[0]['capacity']);
    }

    // ---------------------------------------------------------------- admin isolation

    public function test_campus_admin_only_sees_their_campus_applications(): void
    {
        $mine = $this->makeApplication($this->main, 'M-1');
        $theirs = $this->makeApplication($this->north, 'N-1');

        $this->asAdmin($this->adminMain)
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee($mine->full_name)
            ->assertDontSee($theirs->full_name);

        // Opening the other campus's application directly is a 404.
        $this->asAdmin($this->adminMain)->get(route('applications.show', $theirs))->assertNotFound();
        $this->asAdmin($this->adminNorth)->get(route('applications.show', $theirs))->assertOk();
    }

    public function test_admin_without_a_campus_sees_nothing(): void
    {
        $this->makeApplication($this->main, 'M-1');
        $orphan = $this->makeAdmin('orphan@test.com', null);

        $this->assertSame(1, HostelApplication::count()); // not logged in: unrestricted

        $this->actingAs($orphan);
        $this->assertSame(0, HostelApplication::count());
        $this->assertSame(0, Hostel::count());
    }

    public function test_student_portal_only_shows_hostels_of_the_students_campus(): void
    {
        $this->makeHostel($this->main, 'M1');
        $this->makeHostel($this->north, 'N1');

        $student = Student::create([
            'campus_id' => $this->north->id,
            'admission_number' => 'N-STU-1',
            'full_name' => 'North Student',
            'email' => 'ns@test.com',
            'gender' => 'male',
            'department' => 'CS',
            'semester' => 1,
            'intake' => 'July 2026',
            'contact_number' => '08000000001',
            'emergency_contact' => '08000000002',
            'address' => 'x',
            'status' => 'active',
        ]);

        $response = $this->asStudent($student)->get(route('student.hostels.index'))->assertOk();
        $response->assertSee('NORTH N1')->assertDontSee('MAIN M1');
    }

    // ---------------------------------------------------------------- assignment

    public function test_assignment_creates_a_student_on_the_same_campus_and_charges_the_room_price(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->north, 'N1', 'male', 2, 70000, 130000);
        $application = $this->makeApplication($this->north, 'N-APP-1', 'full_year');

        $this->asAdmin($this->adminNorth)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'bed_id' => $beds[0]->id,
            ])
            ->assertSessionHasNoErrors();

        $student = Student::withoutGlobalScopes()->where('admission_number', 'N-APP-1')->firstOrFail();
        $this->assertSame($this->north->id, $student->campus_id);
        $this->assertSame($room->id, $student->room_id);
        $this->assertEquals(130000, $student->hostel_fee_amount);   // full-year price of THIS room
        $this->assertSame('approved', $application->fresh()->status);
        $this->assertSame(1, $room->fresh()->occupied);
    }

    public function test_a_campus_admin_cannot_assign_a_room_from_another_campus(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->main, 'M1');
        $application = $this->makeApplication($this->north, 'N-APP-2');

        $this->asAdmin($this->adminNorth)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'bed_id' => $beds[0]->id,
            ]);

        $this->assertSame('pending', $application->fresh()->status);
        $this->assertSame(0, $room->fresh()->occupied);
        $this->assertSame(0, Student::withoutGlobalScopes()->count());
    }

    public function test_even_an_unrestricted_master_admin_cannot_cross_campuses(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->main, 'M1');
        $application = $this->makeApplication($this->north, 'N-APP-3');

        $master = SuperAdmin::create([
            'name' => 'Master',
            'email' => 'master@test.com',
            'password' => bcrypt('password'),
            'is_master' => true,
            'is_active' => true,
        ]);

        $this->asMaster($master)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'bed_id' => $beds[0]->id,
            ])
            ->assertSessionHas('error');

        $this->assertSame('pending', $application->fresh()->status);
        $this->assertSame(0, $room->fresh()->occupied);
        $this->assertSame(0, Student::withoutGlobalScopes()->count());
    }

    public function test_a_bed_from_a_different_room_is_rejected(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->main, 'M1');
        [, , $otherBeds] = $this->makeHostel($this->main, 'M2');
        $application = $this->makeApplication($this->main, 'M-APP-1');

        $this->asAdmin($this->adminMain)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'bed_id' => $otherBeds[0]->id,
            ])
            ->assertSessionHas('error');

        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_an_admission_number_already_used_at_another_campus_is_refused(): void
    {
        Student::create([
            'campus_id' => $this->main->id,
            'admission_number' => 'DUP-1',
            'full_name' => 'Existing',
            'email' => 'e@test.com',
            'gender' => 'male',
            'department' => 'CS',
            'semester' => 1,
            'intake' => 'July 2026',
            'contact_number' => '08000000009',
            'emergency_contact' => '08000000008',
            'address' => 'x',
            'status' => 'active',
        ]);

        [$hostel, $room, $beds] = $this->makeHostel($this->north, 'N1');
        $application = $this->makeApplication($this->north, 'DUP-1');

        $this->asAdmin($this->adminNorth)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'bed_id' => $beds[0]->id,
            ])
            ->assertSessionHas('error');

        $this->assertSame('pending', $application->fresh()->status);
    }

    // ---------------------------------------------------------------- super admin

    public function test_only_the_master_super_admin_can_add_a_campus_and_admins_need_one(): void
    {
        $master = SuperAdmin::create([
            'name' => 'Master',
            'email' => 'master@test.com',
            'password' => bcrypt('password'),
            'is_master' => true,
            'is_active' => true,
        ]);

        $this->asMaster($master)
            ->post(route('superadmin.campuses.store'), [
                'name' => 'East Campus',
                'code' => 'east',
                'bank_name' => 'Access',
                'account_number' => '1111111111',
                'account_name' => 'Lincoln East',
                'service_charge_amount' => 5000,
                'service_charge_bank_name' => 'Access',
                'service_charge_account_number' => '2222222222',
                'service_charge_account_name' => 'Lincoln East Services',
                'is_active' => 1,
            ])
            ->assertRedirect(route('superadmin.campuses.index'));

        $this->assertDatabaseHas('campuses', ['code' => 'EAST', 'account_number' => '1111111111']);

        // An admin account must be tied to a campus.
        $this->asMaster($master)
            ->post(route('superadmin.admin-management.store'), [
                'name' => 'No Campus',
                'email' => 'nocampus@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors('campus_id');
    }

    // ---------------------------------------------------------------- uniqueness & input

    public function test_two_campuses_can_use_the_same_hostel_code_and_room_number(): void
    {
        $hostel = fn (string $name) => ['name' => $name, 'code' => 'A1', 'type' => 'male', 'status' => 'active'];

        $this->asAdmin($this->adminMain)->post(route('hostels.store'), $hostel('Main A'))->assertSessionHasNoErrors();
        $this->asAdmin($this->adminNorth)->post(route('hostels.store'), $hostel('North A'))->assertSessionHasNoErrors();
        $this->assertSame(2, Hostel::withoutGlobalScopes()->where('code', 'A1')->count());

        // ...but not twice inside the same campus.
        $this->asAdmin($this->adminMain)->post(route('hostels.store'), $hostel('Main A again'))->assertSessionHasErrors('code');

        $mainHostel = Hostel::withoutGlobalScopes()->where('campus_id', $this->main->id)->where('code', 'A1')->first();
        $northHostel = Hostel::withoutGlobalScopes()->where('campus_id', $this->north->id)->where('code', 'A1')->first();
        $this->assertNotNull($mainHostel);
        $this->assertNotNull($northHostel);

        $room = fn (Hostel $h) => [
            'hostel_id' => $h->id, 'room_number' => '101', 'room_type' => 'double', 'capacity' => 2,
            'price_per_semester' => 80000, 'price_per_year' => 150000, 'floor_number' => 1,
            'gender_type' => 'male', 'status' => 'available',
        ];

        $this->asAdmin($this->adminMain)->post(route('rooms.store'), $room($mainHostel))->assertSessionHasNoErrors();
        $this->asAdmin($this->adminNorth)->post(route('rooms.store'), $room($northHostel))->assertSessionHasNoErrors();
        $this->assertSame(2, Room::withoutGlobalScopes()->where('room_number', '101')->count());

        // Same room number twice in one hostel is still refused.
        $this->asAdmin($this->adminMain)->post(route('rooms.store'), $room($mainHostel))->assertSessionHasErrors('room_number');
    }

    public function test_a_campus_admin_cannot_create_a_room_in_another_campuses_hostel(): void
    {
        [$mainHostel] = $this->makeHostel($this->main, 'M1');

        $this->asAdmin($this->adminNorth)->post(route('rooms.store'), [
            'hostel_id' => $mainHostel->id, 'room_number' => '555', 'room_type' => 'double', 'capacity' => 2,
            'price_per_semester' => 1, 'price_per_year' => 1, 'floor_number' => 1,
            'gender_type' => 'male', 'status' => 'available',
        ])->assertSessionHasErrors('hostel_id');

        $this->assertSame(0, Room::withoutGlobalScopes()->where('room_number', '555')->count());
    }

    public function test_an_admin_cannot_post_a_student_from_another_campus(): void
    {
        $theirs = Student::create([
            'campus_id' => $this->north->id, 'admission_number' => 'N-STU-9', 'full_name' => 'North Person',
            'email' => 'np@test.com', 'gender' => 'male', 'department' => 'CS', 'semester' => 1,
            'intake' => 'July 2026', 'contact_number' => '08000000009', 'emergency_contact' => '08000000008',
            'address' => 'x', 'status' => 'active',
        ]);

        $this->asAdmin($this->adminMain)->post(route('complaints.store'), [
            'student_id' => $theirs->id, 'title' => 'x', 'description' => 'x', 'category' => 'other',
        ])->assertSessionHasErrors('student_id');
    }

    // ---------------------------------------------------------------- student login

    public function test_student_logs_in_with_admission_number_and_phone_number_on_any_campus(): void
    {
        $student = Student::create([
            'campus_id' => $this->north->id, 'admission_number' => 'N-STU-2', 'full_name' => 'North Login',
            'email' => 'nl@test.com', 'gender' => 'male', 'department' => 'CS', 'semester' => 1,
            'intake' => 'July 2026', 'contact_number' => '08031234567', 'emergency_contact' => '08000000002',
            'address' => 'x', 'status' => 'active',
        ]);

        $this->post(route('student.login.post'), ['admission_number' => 'N-STU-2', 'contact_number' => '0803 123 4567'])
            ->assertRedirect();
        $this->assertAuthenticatedAs($student, 'student');
    }

    public function test_student_login_fails_with_the_wrong_phone_number(): void
    {
        Student::create([
            'campus_id' => $this->north->id, 'admission_number' => 'N-STU-3', 'full_name' => 'North Wrong',
            'email' => 'nw@test.com', 'gender' => 'male', 'department' => 'CS', 'semester' => 1,
            'intake' => 'July 2026', 'contact_number' => '08031234567', 'emergency_contact' => '08000000002',
            'address' => 'x', 'status' => 'active',
        ]);

        $this->post(route('student.login.post'), ['admission_number' => 'N-STU-3', 'contact_number' => '08099999999'])
            ->assertSessionHasErrors();
        $this->assertGuest('student');
    }

    public function test_master_can_create_a_campus_admin_with_a_phone_number(): void
    {
        // Regression: users.phone had no migration, so creating any admin returned a 500.
        $master = SuperAdmin::create([
            'name' => 'Master', 'email' => 'm2@test.com', 'password' => bcrypt('password'),
            'is_master' => true, 'is_active' => true,
        ]);

        $this->asMaster($master)->post(route('superadmin.admin-management.store'), [
            'name' => 'North Warden',
            'email' => 'warden@test.com',
            'phone' => '08030000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'campus_id' => $this->north->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'warden@test.com',
            'phone' => '08030000000',
            'campus_id' => $this->north->id,
            'is_admin' => 1,
        ]);
    }

    public function test_unsupported_admin_roles_are_refused_not_a_500(): void
    {
        // The form used to offer Manager/Staff; users.role only allows admin/student, so those 500'd.
        $master = SuperAdmin::create([
            'name' => 'Master', 'email' => 'm3@test.com', 'password' => bcrypt('password'),
            'is_master' => true, 'is_active' => true,
        ]);

        foreach (['manager', 'staff'] as $role) {
            $this->asMaster($master)->post(route('superadmin.admin-management.store'), [
                'name' => 'X', 'email' => "{$role}@test.com", 'password' => 'password123',
                'password_confirmation' => 'password123', 'role' => $role, 'campus_id' => $this->north->id,
            ])->assertSessionHasErrors('role');
        }

        $this->assertDatabaseMissing('users', ['email' => 'manager@test.com']);
    }

    // ---------------------------------------------------------------- service charge + two receipts

    public function test_a_campus_needs_its_own_service_charge_amount_and_account(): void
    {
        $master = SuperAdmin::create([
            'name' => 'Master', 'email' => 'm4@test.com', 'password' => bcrypt('password'),
            'is_master' => true, 'is_active' => true,
        ]);

        $this->asMaster($master)->post(route('superadmin.campuses.store'), [
            'name' => 'West Campus', 'code' => 'west', 'bank_name' => 'Access',
            'account_number' => '3333333333', 'account_name' => 'Lincoln West', 'is_active' => 1,
        ])->assertSessionHasErrors([
            'service_charge_amount', 'service_charge_bank_name',
            'service_charge_account_number', 'service_charge_account_name',
        ]);

        $this->assertDatabaseMissing('campuses', ['code' => 'WEST']);
    }

    public function test_existing_campus_keeps_its_account_for_the_service_charge_but_has_no_amount_yet(): void
    {
        // The migration copies the old single account across; the amount was never stored.
        $this->assertSame($this->main->account_number, $this->main->service_charge_account_number);
        $this->assertSame($this->main->bank_name, $this->main->service_charge_bank_name);
        $this->assertNull($this->main->service_charge_amount);
    }

    public function test_application_form_shows_each_campuss_service_charge_and_the_two_receipt_labels(): void
    {
        $html = $this->get(route('apply'))->assertOk()->getContent();

        // North's own service charge amount and separate account are offered to the page script.
        $this->assertStringContainsString('data-sc-amount="7500.00"', $html);
        $this->assertStringContainsString('data-sc-account-number="9988776655"', $html);
        // ...which differs from North's hostel fee account.
        $this->assertStringContainsString('data-account-number="0123456789"', $html);

        $this->assertStringContainsString('Service Charge Receipt', $html);
        $this->assertStringContainsString('Hostel Fee Receipt', $html);

        // The old hardcoded ₦2,000 application form fee is gone.
        $this->assertStringNotContainsString('2,000', $html);
        $this->assertStringNotContainsString('2000 naira', $html);
    }

    public function test_admin_sees_the_applicants_campus_accounts_next_to_the_receipts(): void
    {
        $application = $this->makeApplication($this->north, 'N-SC-1');

        $this->asAdmin($this->adminNorth)->get(route('applications.show', $application))
            ->assertOk()
            ->assertSee('Service Charge Receipt')
            ->assertSee('9988776655')   // North's service charge account
            ->assertSee('0123456789');  // North's hostel fee account
    }
}
