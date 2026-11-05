<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The service charge can be set per semester, per year, or both (like the hostel fee).
     * service_charge_amount keeps meaning "per semester"; this adds the full-year amount.
     */
    public function up(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->decimal('service_charge_amount_year', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropColumn('service_charge_amount_year');
        });
    }
};
