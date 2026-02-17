<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            // Drop reg_number as it's replaced by student_id
            if (Schema::hasColumn('hostel_applications', 'reg_number')) {
                $table->dropColumn('reg_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('hostel_applications', 'reg_number')) {
                $table->string('reg_number')->nullable();
            }
        });
    }
};
