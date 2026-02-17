<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the incorrect foreign key pointing to users
            // Using a try-catch or checking if it exists is safer but Laravel's dropForeign usually needs the exact name
            try {
                $table->dropForeign(['student_id']);
            } catch (\Exception $e) {
                // If it fails, maybe the name is different or it doesn't exist
            }

            // Add the correct foreign key pointing to students
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
