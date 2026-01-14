<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_rules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category')->default('general'); // general, safety, conduct, facilities, etc.
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('hostel_id')->nullable(); // null means applies to all hostels
            $table->timestamps();
            
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_rules');
    }
};