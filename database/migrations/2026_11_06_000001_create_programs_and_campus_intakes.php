<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The dropdown that used to be called "Department" is really the programme list.
 *
 *  - it becomes `programs`, one list per campus (existing rows go to the first campus)
 *  - intakes become per campus too
 *  - applications: the typed "program" text becomes the typed "department", and the
 *    old dropdown value (stored in `department`) becomes the `program`
 *  - students get a `program` column that takes over the old dropdown value
 */
return new class extends Migration
{
    public function up(): void
    {
        $defaultCampus = DB::table('campuses')->orderBy('id')->value('id');

        // 1. programs (per campus), filled from the old departments list.
        if (!Schema::hasTable('programs')) {
            Schema::create('programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['campus_id', 'name']);
            });
        }

        if (Schema::hasTable('departments')) {
            foreach (DB::table('departments')->orderBy('id')->get() as $row) {
                DB::table('programs')->insert([
                    'campus_id' => $defaultCampus,
                    'name' => $row->name,
                    'is_active' => $row->is_active,
                    'sort_order' => $row->sort_order,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
            Schema::drop('departments');
        }

        // 2. intakes per campus; the same intake name may exist on several campuses.
        if (!Schema::hasColumn('intakes', 'campus_id')) {
            Schema::table('intakes', function (Blueprint $table) {
                $table->dropUnique('intakes_name_unique');
            });
            Schema::table('intakes', function (Blueprint $table) {
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
                $table->unique(['campus_id', 'name']);
            });
        }
        DB::table('intakes')->whereNull('campus_id')->update(['campus_id' => $defaultCampus]);

        // 3. students: new program column takes the old dropdown value.
        if (!Schema::hasColumn('students', 'program')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('program')->nullable();
            });
            DB::table('students')->update([
                'program' => DB::raw('department'),
                'department' => '',
            ]);
        }

        // 4. applications: swap the two values.
        DB::table('hostel_applications')->orderBy('id')->each(function ($row) {
            DB::table('hostel_applications')->where('id', $row->id)->update([
                'program' => $row->department,
                'department' => $row->program ?? '',
            ]);
        });
    }

    public function down(): void
    {
        DB::table('hostel_applications')->orderBy('id')->each(function ($row) {
            DB::table('hostel_applications')->where('id', $row->id)->update([
                'program' => $row->department ?? '',
                'department' => $row->program,
            ]);
        });

        if (Schema::hasColumn('students', 'program')) {
            DB::table('students')->update(['department' => DB::raw('COALESCE(program, \'\')')]);
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('program');
            });
        }

        if (Schema::hasTable('programs')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
            foreach (DB::table('programs')->orderBy('id')->get() as $row) {
                DB::table('departments')->updateOrInsert(['name' => $row->name], [
                    'is_active' => $row->is_active,
                    'sort_order' => $row->sort_order,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
            Schema::drop('programs');
        }
    }
};
