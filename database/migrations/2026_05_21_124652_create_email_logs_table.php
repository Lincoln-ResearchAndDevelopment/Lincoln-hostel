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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email_id')->unique()->index();
            $table->string('email_type')->index();
            $table->string('recipient')->index();
            $table->enum('status', ['attempting', 'sent', 'failed'])->default('attempting')->index();
            $table->text('error_message')->nullable();
            $table->string('error_category')->nullable()->index();
            $table->decimal('duration_ms', 8, 2)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['email_type', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['recipient', 'email_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
