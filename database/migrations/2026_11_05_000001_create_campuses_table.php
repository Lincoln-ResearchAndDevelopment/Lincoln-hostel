<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('address')->nullable();

            // Each campus collects hostel payments into its own account.
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // The existing single-campus installation becomes Campus 1, keeping the
        // payment account that was previously hardcoded in the views.
        DB::table('campuses')->insert([
            'name' => 'Main Campus',
            'code' => 'MAIN',
            'bank_name' => 'Zenith Bank',
            'account_number' => '1311150112',
            'account_name' => 'Lincoln ODL LTD (GOperation)',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
