<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_staff_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->enum('staff_gender', ['male', 'female']);
            $table->enum('assigned_hostel_gender', ['male', 'female']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};