<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campuses whose students pay outside this system (e.g. Kumo) and pay the hostel
        // fee for a set number of semesters depending on whether the course is a health course.
        Schema::table('campuses', function (Blueprint $table) {
            if (!Schema::hasColumn('campuses', 'payments_external')) {
                $table->boolean('payments_external')->default(false);
                $table->unsignedTinyInteger('health_semesters')->default(2);
                $table->unsignedTinyInteger('non_health_semesters')->default(3);
            }
        });
        DB::table('campuses')
            ->where(fn ($q) => $q->whereRaw('LOWER(name) like ?', ['%kumo%'])->orWhereRaw('LOWER(code) like ?', ['%kumo%']))
            ->update(['payments_external' => true]);

        if (!Schema::hasColumn('programs', 'is_health')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->boolean('is_health')->default(false);
            });
        }

        if (!Schema::hasColumn('hostel_applications', 'semesters_to_pay')) {
            Schema::table('hostel_applications', function (Blueprint $table) {
                $table->unsignedTinyInteger('semesters_to_pay')->nullable();
            });
        }

        if (!Schema::hasColumn('students', 'semesters_to_pay')) {
            Schema::table('students', function (Blueprint $table) {
                $table->unsignedTinyInteger('semesters_to_pay')->nullable();
            });
        }

        // One row per student per semester (semester 1 is the application's own receipts).
        if (!Schema::hasTable('semester_payments')) {
            Schema::create('semester_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->unsignedTinyInteger('semester_number');
                $table->string('service_charge_receipt');
                $table->string('hostel_fee_receipt');
                $table->string('status')->default('pending'); // pending | approved | rejected
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->unique(['student_id', 'semester_number']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_payments');

        foreach (['students', 'hostel_applications'] as $t) {
            if (Schema::hasColumn($t, 'semesters_to_pay')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropColumn('semesters_to_pay'));
            }
        }
        if (Schema::hasColumn('programs', 'is_health')) {
            Schema::table('programs', fn (Blueprint $table) => $table->dropColumn('is_health'));
        }
        if (Schema::hasColumn('campuses', 'payments_external')) {
            Schema::table('campuses', fn (Blueprint $table) => $table->dropColumn(['payments_external', 'health_semesters', 'non_health_semesters']));
        }
    }
};
