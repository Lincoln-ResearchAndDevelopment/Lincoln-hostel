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
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->enum('smoking_status', ['non-smoker', 'smoker'])->default('non-smoker')->after('dietary_requirements');
            $table->string('vaccination_status')->nullable()->after('smoking_status');
            $table->string('insurance_info')->nullable()->after('vaccination_status');
            $table->string('preferred_hospital')->nullable()->after('insurance_info');
            $table->text('physical_restrictions')->nullable()->after('preferred_hospital');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->dropColumn(['smoking_status', 'vaccination_status', 'insurance_info', 'preferred_hospital', 'physical_restrictions']);
        });
    }
};
