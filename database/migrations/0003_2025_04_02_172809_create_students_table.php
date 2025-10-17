<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->string('admission_number')->unique();
            $table->string('full_name');
            $table->string('department'); 
            $table->integer('semester');
            $table->string('intake');
            $table->string('contact_number');
            $table->string('emergency_contact');
            $table->text('address');
            $table->date('check_in_date')->nullable();
            $table->date('expected_check_out_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
