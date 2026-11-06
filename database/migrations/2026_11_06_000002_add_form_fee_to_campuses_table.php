<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** One-time application form fee, paid into the service charge account with the service charge. */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('campuses', 'form_fee_amount')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->decimal('form_fee_amount', 12, 2)->nullable()->default(2000);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('campuses', 'form_fee_amount')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->dropColumn('form_fee_amount');
            });
        }
    }
};
