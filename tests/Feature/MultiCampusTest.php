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
            'service_charge_amount_year' => 15000,
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

    /** @var array<int, Hostel> a female hostel with 2-person rooms per campus, for valid submissions */
    private array $payloadHostels = [];

    private function applicationPayload(array $overrides = []): array
    {
        Storage::fake('public');

        // A valid "hostel + room size" choice whenever the submission names an existing campus.
        $choice = [];
        $campus = isset($overrides['campus_id']) ? Campus::find($overrides['campus_id']) : null;
        if ($campus) {
            $this->payloadHostels[$campus->id] ??= $this->makeHostel($campus, 'PAY', 'female', 2)[0];
            \App\Models\Program::withoutGlobalScopes()->firstOrCreate(['campus_id' => $campus->id, 'name' => 'BSc']);
            \App\Models\Intake::withoutGlobalScopes()->firstOrCreate(['campus_id' => $campus->id, 'name' => 'July 2026']);
            $choice = ['preferred_hostel_id' => $this->payloadHostels[$campus->id]->id, 'preferred_room_capacity' => 2];
        }

        return array_merge($choice + [
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

    public function test_room_price_list_is_per_hostel_so_a_bigger_room_can_cost_more_in_one_hostel_than_another(): void
    {
        // Two hostels on the same campus, in different places. The 4-person rooms of the pricey
        // hostel cost MORE than the 2-person rooms of the cheap one - a capacity-only price list
        // (the old behaviour) would hide that.
        [$cheap] = $this->makeHostel($this->main, 'M1', 'male', 2, 50000, 90000);
        [$pricey, $priceyRoom] = $this->makeHostel($this->main, 'M2', 'female', 4, 120000, 230000);
        $cheap->update(['address' => 'Behind the campus gate']);
        $pricey->update(['address' => 'City centre']);
        $this->makeHostel($this->north, 'N1', 'male', 3, 60000, 110000);

        $main = $this->getJson(route('campus.room-prices', $this->main))->assertOk()->json();
        $this->assertCount(2, $main);

        $byName = collect($main)->keyBy('name');
        $this->assertSame('Behind the campus gate', $byName['MAIN M1']['address']);
        $this->assertSame('male', $byName['MAIN M1']['type']);
        $this->assertSame(2, $byName['MAIN M1']['rooms'][0]['capacity']);
        $this->assertEquals(50000, $byName['MAIN M1']['rooms'][0]['semester_min']);

        $this->assertSame('City centre', $byName['MAIN M2']['address']);
        $this->assertSame(4, $byName['MAIN M2']['rooms'][0]['capacity']);
        $this->assertEquals(120000, $byName['MAIN M2']['rooms'][0]['semester_min']);
        $this->assertGreaterThan(
            $byName['MAIN M1']['rooms'][0]['semester_max'],
            $byName['MAIN M2']['rooms'][0]['semester_min']
        );

        // Space left reflects occupancy; a full room group says 0.
        $this->assertSame(4, $byName['MAIN M2']['rooms'][0]['spaces_left']);
        $priceyRoom->update(['occupied' => 4]);
        $again = collect($this->getJson(route('campus.room-prices', $this->main))->json())->keyBy('name');
        $this->assertSame(0, $again['MAIN M2']['rooms'][0]['spaces_left']);

        // Another campus's hostel never leaks in.
        $north = $this->getJson(route('campus.room-prices', $this->north))->assertOk()->json();
        $this->assertCount(1, $north);
        $this->assertSame('NORTH N1', $north[0]['name']);
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
            'email' => 'np@test.com', 'gender' => 'male', 'program' => 'BSc', 'department' => 'CS', 'semester' => 1,
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
            'email' => 'nl@test.com', 'gender' => 'male', 'program' => 'BSc', 'department' => 'CS', 'semester' => 1,
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
            'email' => 'nw@test.com', 'gender' => 'male', 'program' => 'BSc', 'department' => 'CS', 'semester' => 1,
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
            'service_charge_amount', 'service_charge_amount_year', 'service_charge_bank_name',
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

        // North sets both a per-semester and a per-year service charge; the page script labels each.
        $this->assertStringContainsString('data-sc-amount-year="15000.00"', $html);
        $this->assertStringContainsString("' per semester'", $html);
        $this->assertStringContainsString("' per year'", $html);
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
            ->assertSee('7,500.00 per semester')
            ->assertSee('15,000.00 per year')
            ->assertSee('9988776655')   // North's service charge account
            ->assertSee('0123456789');  // North's hostel fee account
    }

    public function test_service_charge_can_be_set_per_semester_per_year_or_both_but_not_neither(): void
    {
        $master = SuperAdmin::create([
            'name' => 'Master', 'email' => 'm5@test.com', 'password' => bcrypt('password'),
            'is_master' => true, 'is_active' => true,
        ]);
        $base = fn (string $code, array $amounts) => array_merge([
            'name' => "Campus $code", 'code' => $code, 'bank_name' => 'Bank', 'account_number' => '1',
            'account_name' => 'Acc', 'service_charge_bank_name' => 'Bank',
            'service_charge_account_number' => '2', 'service_charge_account_name' => 'Svc', 'is_active' => 1,
        ], $amounts);

        // only per semester
        $this->asMaster($master)->post(route('superadmin.campuses.store'), $base('S1', ['service_charge_amount' => 1000]))
            ->assertSessionHasNoErrors();
        // only per year
        $this->asMaster($master)->post(route('superadmin.campuses.store'), $base('Y1', ['service_charge_amount_year' => 2500]))
            ->assertSessionHasNoErrors();
        // both
        $this->asMaster($master)->post(route('superadmin.campuses.store'), $base('B1', ['service_charge_amount' => 1000, 'service_charge_amount_year' => 1800]))
            ->assertSessionHasNoErrors();
        // neither
        $this->asMaster($master)->post(route('superadmin.campuses.store'), $base('N1', []))
            ->assertSessionHasErrors(['service_charge_amount', 'service_charge_amount_year']);

        $this->assertDatabaseHas('campuses', ['code' => 'Y1', 'service_charge_amount' => null, 'service_charge_amount_year' => 2500]);
        $this->assertDatabaseHas('campuses', ['code' => 'B1', 'service_charge_amount' => 1000, 'service_charge_amount_year' => 1800]);
        $this->assertDatabaseMissing('campuses', ['code' => 'N1']);

        // A campus with only a yearly amount still shows on the application form without breaking it.
        $this->get(route('apply'))->assertOk()->assertSee('data-sc-amount-year="2500.00"', false);
    }

    // ---------------------------------------------------------------- hostel choice (hostels price differently)

    public function test_application_must_name_a_hostel_and_room_size(): void
    {
        $payload = $this->applicationPayload(['campus_id' => $this->north->id]);
        unset($payload['preferred_hostel_id'], $payload['preferred_room_capacity']);

        $this->post(route('hostel.apply'), $payload)->assertSessionHasErrors(['preferred_hostel_id', 'preferred_room_capacity']);
        $this->assertSame(0, HostelApplication::withoutGlobalScopes()->count());
    }

    public function test_the_chosen_hostel_and_room_size_are_saved_with_the_application(): void
    {
        [$hostel] = $this->makeHostel($this->north, 'NF', 'female', 3, 90000, 170000);

        $this->post(route('hostel.apply'), $this->applicationPayload([
            'campus_id' => $this->north->id,
            'preferred_hostel_id' => $hostel->id,
            'preferred_room_capacity' => 3,
        ]))->assertSessionHasNoErrors();

        $application = HostelApplication::withoutGlobalScopes()->firstOrFail();
        $this->assertSame($hostel->id, $application->preferred_hostel_id);
        $this->assertSame(3, $application->preferred_room_capacity);
    }

    public function test_a_hostel_from_another_campus_is_refused(): void
    {
        [$mainHostel] = $this->makeHostel($this->main, 'MF', 'female', 2);

        $this->post(route('hostel.apply'), $this->applicationPayload([
            'campus_id' => $this->north->id,
            'preferred_hostel_id' => $mainHostel->id,
        ]))->assertSessionHasErrors('preferred_hostel_id');

        $this->assertSame(0, HostelApplication::withoutGlobalScopes()->count());
    }

    public function test_a_hostel_for_the_other_gender_is_refused_but_a_mixed_hostel_is_fine(): void
    {
        [$maleHostel] = $this->makeHostel($this->north, 'NM', 'male', 2);
        [$mixedHostel] = $this->makeHostel($this->north, 'NX', 'mixed', 2);

        // applicant is female (payload default)
        $this->post(route('hostel.apply'), $this->applicationPayload([
            'campus_id' => $this->north->id, 'preferred_hostel_id' => $maleHostel->id,
        ]))->assertSessionHasErrors('preferred_hostel_id');

        $this->post(route('hostel.apply'), $this->applicationPayload([
            'campus_id' => $this->north->id, 'preferred_hostel_id' => $mixedHostel->id,
        ]))->assertSessionHasNoErrors();
    }

    public function test_a_room_size_the_hostel_does_not_have_is_refused(): void
    {
        [$hostel] = $this->makeHostel($this->north, 'NF', 'female', 2);   // only 2-person rooms

        $this->post(route('hostel.apply'), $this->applicationPayload([
            'campus_id' => $this->north->id,
            'preferred_hostel_id' => $hostel->id,
            'preferred_room_capacity' => 6,
        ]))->assertSessionHasErrors('preferred_room_capacity');
    }

    public function test_expected_fee_follows_the_hostel_room_size_and_payment_plan(): void
    {
        [$a] = $this->makeHostel($this->north, 'NA', 'female', 2, 50000, 90000);
        [$b] = $this->makeHostel($this->north, 'NB', 'female', 4, 120000, 230000);

        $make = fn (Hostel $h, int $cap, string $plan) => HostelApplication::factory()->create([
            'campus_id' => $this->north->id, 'preferred_hostel_id' => $h->id,
            'preferred_room_capacity' => $cap, 'amount_paid' => $plan,
        ]);

        $this->assertEquals(['min' => 50000.0, 'max' => 50000.0, 'plan' => 'semester'], $make($a, 2, 'semester')->expectedFee());
        $this->assertEquals(['min' => 230000.0, 'max' => 230000.0, 'plan' => 'full_year'], $make($b, 4, 'full_year')->expectedFee());
        $this->assertNull($make($a, 9, 'semester')->expectedFee());   // no such room size there
        $this->assertNull(HostelApplication::factory()->create(['campus_id' => $this->north->id])->expectedFee()); // older application
    }

    public function test_admin_sees_the_hostel_paid_for_and_it_is_preselected_for_allocation(): void
    {
        [$wanted] = $this->makeHostel($this->north, 'NW', 'female', 2, 50000, 90000);
        $wanted->update(['name' => 'Palm Court', 'address' => '5 Palm Road']);
        [$mixed] = $this->makeHostel($this->north, 'NX', 'mixed', 2);
        $mixed->update(['name' => 'Open House']);
        $this->makeHostel($this->north, 'NM', 'male', 2)[0]->update(['name' => 'Boys Lodge']);

        $application = HostelApplication::factory()->create([
            'campus_id' => $this->north->id, 'gender' => 'female', 'status' => 'pending',
            'preferred_hostel_id' => $wanted->id, 'preferred_room_capacity' => 2, 'amount_paid' => 'semester',
        ]);

        $html = $this->asAdmin($this->adminNorth)->get(route('applications.show', $application))
            ->assertOk()
            ->assertSee('Palm Court')
            ->assertSee('5 Palm Road')
            ->assertSee('Expected hostel fee')
            ->assertSee('50,000.00')
            ->assertSee('Open House')        // mixed hostels can now be chosen
            ->assertDontSee('Boys Lodge')    // other gender still hidden
            ->getContent();

        $this->assertMatchesRegularExpression('/value="' . $wanted->id . '"\s+selected/', $html);
    }

    public function test_allocating_to_a_different_hostel_than_the_one_paid_for_is_flagged(): void
    {
        [$paidFor] = $this->makeHostel($this->north, 'NA', 'female', 2, 50000, 90000);
        [$other, $otherRoom, $otherBeds] = $this->makeHostel($this->north, 'NB', 'female', 2, 120000, 230000);
        $other->update(['name' => 'Expensive Hall']);
        $paidFor->update(['name' => 'Cheap Hall']);

        $application = HostelApplication::factory()->create([
            'campus_id' => $this->north->id, 'gender' => 'female', 'status' => 'pending', 'student_id' => 'N-DIFF-1',
            'preferred_hostel_id' => $paidFor->id, 'preferred_room_capacity' => 2, 'amount_paid' => 'semester',
        ]);

        $response = $this->asAdmin($this->adminNorth)->post(route('applications.approve-assign', $application), [
            'hostel_id' => $other->id, 'room_id' => $otherRoom->id, 'bed_id' => $otherBeds[0]->id,
        ]);

        $response->assertSessionHasNoErrors();
        $message = session('success');
        $this->assertStringContainsString('Cheap Hall', $message);
        $this->assertStringContainsString('Expensive Hall', $message);
        $this->assertStringContainsString('check the fee difference', $message);
    }

    public function test_application_page_offers_the_hostel_and_room_size_choices(): void
    {
        $this->get(route('apply'))->assertOk()
            ->assertSee('name="preferred_hostel_id"', false)
            ->assertSee('name="preferred_room_capacity"', false)
            ->assertSee('Choose your hostel');
    }

    public function test_academic_options_list_only_that_campuses_programs_and_intakes(): void
    {
        \App\Models\Program::withoutGlobalScopes()->create(['campus_id' => $this->north->id, 'name' => 'Nursing']);
        \App\Models\Program::withoutGlobalScopes()->create(['campus_id' => $this->main->id, 'name' => 'Law']);
        \App\Models\Intake::withoutGlobalScopes()->create(['campus_id' => $this->main->id, 'name' => 'May 2027']);

        $this->getJson("/campuses/{$this->north->id}/academic-options")->assertOk()
            ->assertJson(['programs' => ['Nursing'], 'intakes' => []]);
        $this->getJson("/campuses/{$this->main->id}/academic-options")->assertOk()
            ->assertJson(['programs' => ['Law'], 'intakes' => ['May 2027']]);
    }

    public function test_application_rejects_a_program_from_another_campus_and_department_is_optional(): void
    {
        \App\Models\Program::withoutGlobalScopes()->create(['campus_id' => $this->main->id, 'name' => 'Law']);

        $this->post('/hostel/apply', $this->applicationPayload(['campus_id' => $this->north->id, 'program' => 'Law']))
            ->assertSessionHasErrors('program');

        $this->post('/hostel/apply', $this->applicationPayload(['campus_id' => $this->north->id, 'department' => null]))
            ->assertSessionHasNoErrors();
        $this->assertSame('', HostelApplication::withoutGlobalScopes()->latest('id')->first()->department);
    }

    public function test_program_and_intake_lists_are_managed_per_campus(): void
    {
        $this->asAdmin($this->adminNorth)->post('/admin/programs', ['name' => 'Nursing'])->assertSessionHasNoErrors();
        $this->asAdmin($this->adminNorth)->post('/admin/intakes', ['name' => 'July 2026'])->assertSessionHasNoErrors();

        $this->assertSame($this->north->id, (int) \App\Models\Program::withoutGlobalScopes()->where('name', 'Nursing')->value('campus_id'));
        $this->assertSame($this->north->id, (int) \App\Models\Intake::withoutGlobalScopes()->where('name', 'July 2026')->value('campus_id'));

        // Same names are fine on another campus, and each admin only sees their own list.
        $this->asAdmin($this->adminMain)->post('/admin/programs', ['name' => 'Nursing'])->assertSessionHasNoErrors();
        $this->assertSame(1, \App\Models\Program::withoutGlobalScopes()->where('campus_id', $this->main->id)->count());
        $this->asAdmin($this->adminMain)->get('/admin/programs')->assertOk()->assertSee('Nursing');
    }

    public function test_approval_copies_program_and_department_to_the_student(): void
    {
        [$hostel, $room, $beds] = $this->makeHostel($this->north, 'PRG', 'male', 2);
        $application = $this->makeApplication($this->north, 'N-PRG-1');
        $application->update(['program' => 'Nursing', 'department' => 'Health Sciences']);

        $this->asAdmin($this->adminNorth)
            ->post(route('applications.approve-assign', $application), [
                'hostel_id' => $hostel->id, 'room_id' => $room->id, 'bed_id' => $beds[0]->id,
            ])->assertSessionHasNoErrors();

        $student = Student::withoutGlobalScopes()->where('admission_number', 'N-PRG-1')->firstOrFail();
        $this->assertSame('Nursing', $student->program);
        $this->assertSame('Health Sciences', $student->department);
    }

    public function test_migration_swaps_old_program_and_department_values(): void
    {
        $application = $this->makeApplication($this->north, 'N-OLD-1');
        // Old meaning: `program` was typed text, `department` was the dropdown value.
        $application->update(['program' => 'BSc typed', 'department' => 'Nursing']);

        $migration = require base_path('database/migrations/2026_11_06_000001_create_programs_and_campus_intakes.php');
        $migration->up();

        $fresh = HostelApplication::withoutGlobalScopes()->find($application->id);
        $this->assertSame('Nursing', $fresh->program);
        $this->assertSame('BSc typed', $fresh->department);
    }
}
