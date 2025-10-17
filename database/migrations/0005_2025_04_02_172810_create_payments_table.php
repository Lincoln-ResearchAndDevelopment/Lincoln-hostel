<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id');
        $table->decimal('amount', 10, 2);
        $table->string('payment_method');
        $table->string('receipt_path')->nullable();  // This field stores the relative path
        $table->text('notes')->nullable();
        $table->string('status')->default('pending');
        $table->timestamp('payment_date')->nullable();
        $table->timestamps();      
        $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        $table->boolean('is_read')->default(false);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
