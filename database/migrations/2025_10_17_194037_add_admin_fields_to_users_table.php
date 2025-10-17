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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('role');
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->json('permissions')->nullable()->after('is_active');
            $table->unsignedBigInteger('created_by')->nullable()->after('permissions');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('activated_by')->nullable()->after('updated_by');
            $table->unsignedBigInteger('deactivated_by')->nullable()->after('activated_by');
            $table->timestamp('activated_at')->nullable()->after('deactivated_by');
            $table->timestamp('deactivated_at')->nullable()->after('activated_at');
            $table->timestamp('last_login_at')->nullable()->after('deactivated_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->integer('login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('activated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deactivated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['activated_by']);
            $table->dropForeign(['deactivated_by']);

            $table->dropColumn([
                'is_admin',
                'is_active',
                'permissions',
                'created_by',
                'updated_by',
                'activated_by',
                'deactivated_by',
                'activated_at',
                'deactivated_at',
                'last_login_at',
                'last_login_ip',
                'login_attempts',
                'locked_until'
            ]);
        });
    }
};
