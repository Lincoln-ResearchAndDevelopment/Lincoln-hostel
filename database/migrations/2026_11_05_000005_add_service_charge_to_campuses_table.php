<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Applicants upload two receipts: the hostel fee and the service charge. Each campus has
     * its own service charge amount and pays it into its own (separate) account.
     * The existing bank_name / account_number / account_name columns stay the HOSTEL FEE account.
     */
    public function up(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->decimal('service_charge_amount', 12, 2)->nullable();
            $table->string('service_charge_bank_name')->nullable();
            $table->string('service_charge_account_number')->nullable();
            $table->string('service_charge_account_name')->nullable();
        });

        // Until now every payment went into one account per campus, so keep that account for the
        // service charge on existing campuses; the amount is left empty on purpose (it was never
        // stored) and must be set by the super admin.
        DB::table('campuses')->update([
            'service_charge_bank_name' => DB::raw('bank_name'),
            'service_charge_account_number' => DB::raw('account_number'),
            'service_charge_account_name' => DB::raw('account_name'),
        ]);
    }

    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropColumn([
                'service_charge_amount',
                'service_charge_bank_name',
                'service_charge_account_number',
                'service_charge_account_name',
            ]);
        });
    }
};
