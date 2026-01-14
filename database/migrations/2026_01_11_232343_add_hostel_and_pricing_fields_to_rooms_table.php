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
        Schema::table('rooms', function (Blueprint $table) {
            // Add hostel relationship
            $table->foreignId('hostel_id')->nullable()->after('id')->constrained('hostels')->onDelete('cascade');
            
            // Add room type and pricing
            $table->enum('room_type', ['single', 'double', 'triple', 'quad', 'dormitory'])->default('double')->after('room_number');
            $table->decimal('price_per_semester', 10, 2)->default(0)->after('room_type');
            $table->decimal('price_per_year', 10, 2)->default(0)->after('price_per_semester');
            
            // Add floor and facilities
            $table->integer('floor_number')->default(1)->after('price_per_year');
            $table->json('facilities')->nullable()->after('floor_number');
            $table->json('images')->nullable()->after('facilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropColumn([
                'hostel_id',
                'room_type',
                'price_per_semester',
                'price_per_year',
                'floor_number',
                'facilities',
                'images'
            ]);
        });
    }
};
