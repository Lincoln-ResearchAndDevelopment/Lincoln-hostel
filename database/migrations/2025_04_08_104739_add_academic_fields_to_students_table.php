<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'department')) {
                $table->string('department')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('students', 'semester')) {
                $table->integer('semester')->nullable()->after('department');
            }
            if (!Schema::hasColumn('students', 'intake')) {
                $table->string('intake')->nullable()->after('semester');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('students', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('students', 'intake')) {
                $table->dropColumn('intake');
            }
        });
    }
};
