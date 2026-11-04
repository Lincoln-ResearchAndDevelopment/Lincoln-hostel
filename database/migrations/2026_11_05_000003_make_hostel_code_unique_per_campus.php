<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hostel codes were unique across the whole system. With several campuses each
     * one needs its own "A", "BH1", etc., so uniqueness moves to (campus, code).
     */
    public function up(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropUnique('hostels_code_unique');
            $table->unique(['campus_id', 'code'], 'hostels_campus_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropUnique('hostels_campus_code_unique');
            $table->unique('code', 'hostels_code_unique');
        });
    }
};
