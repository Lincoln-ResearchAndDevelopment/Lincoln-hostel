<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            // Add student_id field after reg_number
            if (!Schema::hasColumn('hostel_applications', 'student_id')) {
                $table->string('student_id')->nullable()->after('reg_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            if (Schema::hasColumn('hostel_applications', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }
};
