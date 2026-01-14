<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('leave_requests', 'destination')) {
                $table->string('destination')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('leave_requests', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('leave_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact', 'destination', 'approved_by', 'approved_at']);
        });
    }
};
