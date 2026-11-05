<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hostels are in different places and price their rooms differently (a bigger room in one
     * hostel can cost more than a smaller room in another), so an application has to record
     * WHICH hostel and room size the applicant paid for. The admin checks the receipt against
     * that choice and allocates accordingly. Nullable: applications made before this existed
     * have no recorded choice.
     */
    public function up(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->foreignId('preferred_hostel_id')->nullable()->constrained('hostels')->nullOnDelete();
            $table->unsignedSmallInteger('preferred_room_capacity')->nullable(); // students per room
        });
    }

    public function down(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['preferred_hostel_id']);
            }
            $table->dropColumn(['preferred_hostel_id', 'preferred_room_capacity']);
        });
    }
};
