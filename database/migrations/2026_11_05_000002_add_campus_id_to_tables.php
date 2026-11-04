<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that get a campus_id column.
     */
    private array $tables = [
        'hostels',
        'rooms',
        'beds',
        'hostel_applications',
        'students',
        'payments',
        'complaints',
        'visitors',
        'leave_requests',
        'attendance_records',
        'staff',
        'announcements',
        'hostel_rules',
        'users',
    ];

    /**
     * Tables whose rows belong to a student: campus comes from the student.
     */
    private array $viaStudent = ['payments', 'complaints', 'visitors', 'leave_requests', 'attendance_records'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'campus_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            });
        }

        $this->backfill();
    }

    /**
     * Everything that exists today belongs to the first campus.
     */
    private function backfill(): void
    {
        $defaultId = DB::table('campuses')->orderBy('id')->value('id');

        // Parents first, then children derived from them.
        foreach (['hostels', 'hostel_applications', 'students', 'staff', 'announcements'] as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->whereNull('campus_id')->update(['campus_id' => $defaultId]);
            }
        }

        // Only admin-type accounts are tied to a campus (student login accounts are not).
        DB::table('users')
            ->whereNull('campus_id')
            ->where(function ($q) {
                $q->where('is_admin', true)->orWhereIn('role', ['admin', 'manager', 'staff']);
            })
            ->update(['campus_id' => $defaultId]);

        DB::table('rooms')->whereNull('campus_id')->update([
            'campus_id' => DB::raw('(select h.campus_id from hostels h where h.id = rooms.hostel_id)'),
        ]);
        DB::table('rooms')->whereNull('campus_id')->update(['campus_id' => $defaultId]);

        DB::table('beds')->whereNull('campus_id')->update([
            'campus_id' => DB::raw('(select r.campus_id from rooms r where r.id = beds.room_id)'),
        ]);
        DB::table('beds')->whereNull('campus_id')->update(['campus_id' => $defaultId]);

        foreach ($this->viaStudent as $t) {
            if (!Schema::hasTable($t)) {
                continue;
            }
            DB::table($t)->whereNull('campus_id')->update([
                'campus_id' => DB::raw("(select s.campus_id from students s where s.id = {$t}.student_id)"),
            ]);
            DB::table($t)->whereNull('campus_id')->update(['campus_id' => $defaultId]);
        }

        // hostel_rules stay NULL on purpose: NULL means "applies to every campus".
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'campus_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (DB::getDriverName() !== 'sqlite') {
                        $table->dropForeign(['campus_id']);
                    }
                    $table->dropColumn('campus_id');
                });
            }
        }
    }
};
