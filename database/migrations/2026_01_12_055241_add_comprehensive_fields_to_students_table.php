<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal Information
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('students', 'nationality')) {
                $table->string('nationality')->default('Nigerian')->after('date_of_birth');
            }
            if (!Schema::hasColumn('students', 'state_of_origin')) {
                $table->string('state_of_origin')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('students', 'local_government')) {
                $table->string('local_government')->nullable()->after('state_of_origin');
            }
            if (!Schema::hasColumn('students', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('local_government');
            }
            
            // Parent/Guardian Information
            if (!Schema::hasColumn('students', 'parent_name')) {
                $table->string('parent_name')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('students', 'parent_relationship')) {
                $table->string('parent_relationship')->nullable()->after('parent_name');
            }
            if (!Schema::hasColumn('students', 'parent_phone')) {
                $table->string('parent_phone')->nullable()->after('parent_relationship');
            }
            if (!Schema::hasColumn('students', 'parent_email')) {
                $table->string('parent_email')->nullable()->after('parent_phone');
            }
            if (!Schema::hasColumn('students', 'parent_address')) {
                $table->text('parent_address')->nullable()->after('parent_email');
            }
            if (!Schema::hasColumn('students', 'parent_occupation')) {
                $table->string('parent_occupation')->nullable()->after('parent_address');
            }
            
            // Medical Information
            if (!Schema::hasColumn('students', 'blood_group')) {
                $table->string('blood_group')->nullable()->after('parent_occupation');
            }
            if (!Schema::hasColumn('students', 'genotype')) {
                $table->string('genotype')->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('students', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable()->after('genotype');
            }
            if (!Schema::hasColumn('students', 'allergies')) {
                $table->text('allergies')->nullable()->after('medical_conditions');
            }
            if (!Schema::hasColumn('students', 'medications')) {
                $table->text('medications')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('students', 'has_disability')) {
                $table->boolean('has_disability')->default(false)->after('medications');
            }
            if (!Schema::hasColumn('students', 'disability_details')) {
                $table->text('disability_details')->nullable()->after('has_disability');
            }
            
            // Hostel Fee Information
            if (!Schema::hasColumn('students', 'hostel_fee_amount')) {
                $table->decimal('hostel_fee_amount', 12, 2)->default(0)->after('disability_details');
            }
            if (!Schema::hasColumn('students', 'hostel_fee_paid')) {
                $table->decimal('hostel_fee_paid', 12, 2)->default(0)->after('hostel_fee_amount');
            }
            if (!Schema::hasColumn('students', 'hostel_fee_status')) {
                $table->enum('hostel_fee_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('hostel_fee_paid');
            }
            if (!Schema::hasColumn('students', 'payment_due_date')) {
                $table->date('payment_due_date')->nullable()->after('hostel_fee_status');
            }
            
            // Application Reference
            if (!Schema::hasColumn('students', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable()->after('payment_due_date');
            }
            
            // Notification Preferences
            if (!Schema::hasColumn('students', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('application_id');
            }
            
            // Last Activity Tracking
            if (!Schema::hasColumn('students', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('notification_preferences');
            }
            if (!Schema::hasColumn('students', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'email', 'date_of_birth', 'nationality', 'state_of_origin', 'local_government', 'profile_photo',
                'parent_name', 'parent_relationship', 'parent_phone', 'parent_email', 'parent_address', 'parent_occupation',
                'blood_group', 'genotype', 'medical_conditions', 'allergies', 'medications', 'has_disability', 'disability_details',
                'hostel_fee_amount', 'hostel_fee_paid', 'hostel_fee_status', 'payment_due_date',
                'application_id', 'notification_preferences', 'last_login_at', 'last_login_ip'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};