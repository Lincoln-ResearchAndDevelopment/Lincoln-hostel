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
        Schema::table('sessions', function (Blueprint $table) {
            // Add columns for better session tracking and security
            $table->string('guard_type')->nullable()->after('user_id')->index();
            $table->json('auth_contexts')->nullable()->after('guard_type');
            $table->timestamp('created_at')->nullable()->after('last_activity');
            $table->timestamp('expires_at')->nullable()->after('created_at')->index();
            
            // Add indexes for better performance
            $table->index(['guard_type', 'user_id']);
            $table->index(['ip_address', 'last_activity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['guard_type', 'user_id']);
            $table->dropIndex(['ip_address', 'last_activity']);
            $table->dropColumn(['guard_type', 'auth_contexts', 'created_at', 'expires_at']);
        });
    }
};
