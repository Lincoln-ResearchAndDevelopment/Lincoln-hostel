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
        Schema::create('hostel_applications', function (Blueprint $table) {
            $table->id();
            
            // Application Status
            $table->string('application_number')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            // Academic Information
            $table->string('academic_year');
            $table->string('amount_paid');
            
            // Student Information
            $table->string('full_name');
            $table->string('reg_number');
            $table->string('intake');
            $table->string('program');
            $table->string('department');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('phone_number');
            $table->string('email');
            $table->text('home_address');
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin');
            $table->string('local_government');
            
            // Parent/Guardian Information
            $table->string('parent_full_name');
            $table->string('parent_relationship');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->text('parent_address');
            $table->string('parent_occupation');
            $table->string('parent_workplace')->nullable();
            
            // Emergency Contact (can be different from parent)
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relationship');
            $table->text('emergency_contact_address');
            
            // Medical Information
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();
            
            // Accommodation Preferences
            $table->string('preferred_hostel_type')->nullable(); // male, female, mixed
            $table->string('preferred_room_type')->nullable(); // single, double, triple, quad
            $table->text('special_accommodation_needs')->nullable();
            
            // Documents
            $table->string('passport_photo')->nullable();
            $table->string('applicationform_receipt')->nullable();
            $table->string('hostelfee_receipt')->nullable();
            $table->string('medical_report')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('admission_letter')->nullable();
            
            // Declaration and Signatures
            $table->string('declaration_name');
            $table->string('applicant_signature');
            $table->date('applicant_date');
            $table->string('guardian_signature');
            $table->date('guardian_date');
            
            // Additional Information
            $table->text('previous_hostel_experience')->nullable();
            $table->text('why_choose_hostel')->nullable();
            $table->json('references')->nullable(); // For character references
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('reg_number');
            $table->index('application_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_applications');
    }
};
