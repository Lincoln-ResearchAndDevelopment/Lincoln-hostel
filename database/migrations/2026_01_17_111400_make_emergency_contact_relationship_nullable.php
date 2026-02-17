<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->string('emergency_contact_relationship')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->string('emergency_contact_relationship')->nullable(false)->change();
        });
    }
};
