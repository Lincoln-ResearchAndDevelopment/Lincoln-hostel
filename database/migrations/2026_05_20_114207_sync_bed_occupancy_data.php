<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Bed;
use App\Models\Student;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration fixes data inconsistencies between beds and students:
     * 1. Beds marked occupied but no student assigned
     * 2. Beds with student_id but not marked occupied
     * 3. Students with bed_id but bed doesn't reference them back
     * 4. Students with room_id but no bed_id
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Fix 1: Beds marked occupied but no student assigned
            $orphanedBeds = Bed::where('is_occupied', true)
                ->whereNull('student_id')
                ->get();

            foreach ($orphanedBeds as $bed) {
                $bed->update(['is_occupied' => false]);
                \Log::info("Migration: Fixed orphaned bed {$bed->id} - marked as available");
            }

            // Fix 2: Beds with student_id but not marked occupied
            $unmarkedBeds = Bed::where('is_occupied', false)
                ->whereNotNull('student_id')
                ->get();

            foreach ($unmarkedBeds as $bed) {
                $bed->update(['is_occupied' => true]);
                \Log::info("Migration: Fixed unmarked bed {$bed->id} - marked as occupied");
            }

            // Fix 3: Students with bed_id but bed doesn't reference them
            $studentsWithBeds = Student::whereNotNull('bed_id')->get();
            
            foreach ($studentsWithBeds as $student) {
                $bed = Bed::find($student->bed_id);
                
                if (!$bed) {
                    // Bed doesn't exist - clear student's bed_id
                    $student->update(['bed_id' => null]);
                    \Log::warning("Migration: Student {$student->id} had invalid bed_id - cleared");
                    continue;
                }

                if ($bed->student_id != $student->id) {
                    // Bed exists but doesn't reference this student
                    $bed->update([
                        'student_id' => $student->id,
                        'is_occupied' => true,
                    ]);
                    \Log::info("Migration: Fixed bed {$bed->id} - updated to reference student {$student->id}");
                }
            }

            // Fix 4: Students with room_id but no bed_id (assign first available bed)
            $studentsWithoutBeds = Student::whereNotNull('room_id')
                ->whereNull('bed_id')
                ->get();

            foreach ($studentsWithoutBeds as $student) {
                $availableBed = Bed::where('room_id', $student->room_id)
                    ->where('is_occupied', false)
                    ->first();

                if ($availableBed) {
                    $availableBed->update([
                        'is_occupied' => true,
                        'student_id' => $student->id,
                    ]);
                    
                    $student->update(['bed_id' => $availableBed->id]);
                    
                    \Log::info("Migration: Assigned bed {$availableBed->id} to student {$student->id}");
                } else {
                    \Log::warning("Migration: Student {$student->id} in room {$student->room_id} has no available bed");
                }
            }

            \Log::info("Migration: Bed occupancy sync completed");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only fixes data - no schema changes to reverse
        \Log::info("Migration: Bed occupancy sync rollback - no action needed");
    }
};
