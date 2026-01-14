<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->enum('type', ['check_in', 'check_out']);
            $table->timestamp('recorded_at');
            $table->string('recorded_by')->nullable(); // porter name or system
            $table->text('notes')->nullable();
            $table->string('location')->nullable(); // gate, main entrance, etc.
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->index(['student_id', 'recorded_at']);
            $table->index(['type', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};