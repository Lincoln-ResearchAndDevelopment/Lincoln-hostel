<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BedAssignmentService
 * 
 * Handles all bed assignment operations atomically to maintain data integrity.
 * This service ensures that room occupancy, bed status, and student assignments
 * remain synchronized across all operations.
 * 
 * Critical Rules:
 * - All operations MUST run in database transactions
 * - Room occupancy MUST be updated atomically using DB::raw()
 * - Bed status (is_occupied, student_id) MUST stay in sync
 * - Student bed_id MUST be updated when bed changes
 */
class BedAssignmentService
{
    /**
     * Assign a bed to a student atomically.
     * 
     * This handles:
     * - Releasing old bed if student had one
     * - Validating new bed availability
     * - Updating room occupancy counts
     * - Assigning new bed to student
     * 
     * @param Student $student
     * @param int|null $newBedId
     * @return array ['success' => bool, 'message' => string]
     * @throws \Exception
     */
    public function assignBed(Student $student, ?int $newBedId): array
    {
        return DB::transaction(function () use ($student, $newBedId) {
            $oldBedId = $student->bed_id;
            $oldRoomId = $student->room_id;

            // If no change, return early
            if ($oldBedId == $newBedId) {
                return ['success' => true, 'message' => 'No bed change required.'];
            }

            // Release old bed if exists
            if ($oldBedId) {
                $this->releaseBed($oldBedId, $student->id);
            }

            // If new bed is null, just unassign (student has no bed and no room)
            if (!$newBedId) {
                if ($oldRoomId) {
                    Room::where('id', $oldRoomId)
                        ->where('occupied', '>', 0)
                        ->update([
                            'occupied' => DB::raw('occupied - 1'),
                            'status' => DB::raw('CASE WHEN occupied - 1 < capacity THEN "available" ELSE status END'),
                        ]);
                }

                $student->update([
                    'bed_id' => null,
                    'room_id' => null,
                ]);

                Log::info("Bed and Room unassigned: Student {$student->admission_number} unassigned from Bed (ID: {$oldBedId}) and Room (ID: {$oldRoomId})");

                return ['success' => true, 'message' => 'Bed and room unassigned successfully.'];
            }

            // Validate and assign new bed
            $newBed = Bed::lockForUpdate()->find($newBedId);
            
            if (!$newBed) {
                throw new \Exception('Bed not found.');
            }

            if ($newBed->is_occupied && $newBed->student_id != $student->id) {
                $occupant = Student::find($newBed->student_id);
                $occupantName = $occupant ? $occupant->full_name : 'another student';
                throw new \Exception("Bed {$newBed->bed_number} is already occupied by {$occupantName}.");
            }

            $newRoomId = $newBed->room_id;

            // Update room occupancy if room changed
            if ($oldRoomId != $newRoomId) {
                // Decrement old room
                if ($oldRoomId) {
                    Room::where('id', $oldRoomId)
                        ->where('occupied', '>', 0)
                        ->update([
                            'occupied' => DB::raw('occupied - 1'),
                            'status' => DB::raw('CASE WHEN occupied - 1 < capacity THEN "available" ELSE status END'),
                        ]);
                }

                // Increment new room
                $newRoom = Room::lockForUpdate()->find($newRoomId);
                if ($newRoom->occupied >= $newRoom->capacity) {
                    throw new \Exception('Room is already at full capacity.');
                }

                Room::where('id', $newRoomId)->update([
                    'occupied' => DB::raw('occupied + 1'),
                    'status' => DB::raw('CASE WHEN occupied + 1 >= capacity THEN "full" ELSE "available" END'),
                ]);
            }

            // Assign bed to student
            $newBed->update([
                'is_occupied' => true,
                'student_id' => $student->id,
            ]);

            // Update student record
            $student->update([
                'bed_id' => $newBedId,
                'room_id' => $newRoomId,
            ]);

            Log::info("Bed assigned: Student {$student->admission_number} assigned to Bed {$newBed->bed_number} in Room {$newRoom->room_number}");

            return ['success' => true, 'message' => 'Bed assigned successfully.'];
        });
    }

    /**
     * Release a bed (mark as unoccupied).
     * 
     * @param int $bedId
     * @param int|null $expectedStudentId For validation
     * @return void
     */
    protected function releaseBed(int $bedId, ?int $expectedStudentId = null): void
    {
        $bed = Bed::lockForUpdate()->find($bedId);
        
        if (!$bed) {
            return; // Bed doesn't exist, nothing to release
        }

        // Validate that the bed belongs to the expected student
        if ($expectedStudentId && $bed->student_id != $expectedStudentId) {
            Log::warning("Bed release mismatch: Bed {$bedId} is assigned to student {$bed->student_id}, not {$expectedStudentId}");
        }

        $bed->update([
            'is_occupied' => false,
            'student_id' => null,
        ]);

        Log::info("Bed released: Bed {$bed->bed_number} (ID: {$bedId}) is now available.");
    }

    /**
     * Get available beds for a specific room.
     * 
     * @param int $roomId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableBeds(int $roomId)
    {
        return Bed::where('room_id', $roomId)
            ->where('is_occupied', false)
            ->orderBy('bed_number')
            ->get();
    }

    /**
     * Get available rooms filtered by gender with bed availability info.
     * 
     * @param string $gender 'male' or 'female'
     * @param int|null $currentRoomId Include current room even if full
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableRoomsWithBeds(string $gender, ?int $currentRoomId = null)
    {
        return Room::with(['hostel', 'beds' => function ($query) {
                $query->where('is_occupied', false);
            }])
            ->where('gender_type', $gender)
            ->where(function ($query) use ($currentRoomId) {
                $query->where(function ($q) {
                    $q->where('status', 'available')
                      ->whereRaw('occupied < capacity');
                })
                ->when($currentRoomId, function ($q) use ($currentRoomId) {
                    $q->orWhere('id', $currentRoomId);
                });
            })
            ->get()
            ->map(function ($room) {
                $availableBeds = $room->beds->count();
                $room->available_beds_count = $availableBeds;
                $room->display_name = "{$room->hostel->name} - {$room->room_number} ({$room->gender_type}) [{$availableBeds}/{$room->capacity} available]";
                return $room;
            })
            ->sortBy('display_name');
    }

    /**
     * Sync bed occupancy status with actual student assignments.
     * Used for data integrity fixes.
     * 
     * @return array ['fixed' => int, 'errors' => array]
     */
    public function syncBedOccupancy(): array
    {
        $fixed = 0;
        $errors = [];

        DB::transaction(function () use (&$fixed, &$errors) {
            // Fix beds marked occupied but no student assigned
            $orphanedBeds = Bed::where('is_occupied', true)
                ->whereNull('student_id')
                ->get();

            foreach ($orphanedBeds as $bed) {
                $bed->update(['is_occupied' => false]);
                $fixed++;
                Log::info("Fixed orphaned bed: Bed {$bed->id} marked as available.");
            }

            // Fix beds with student_id but not marked occupied
            $unmarkeddBeds = Bed::where('is_occupied', false)
                ->whereNotNull('student_id')
                ->get();

            foreach ($unmarkeddBeds as $bed) {
                $bed->update(['is_occupied' => true]);
                $fixed++;
                Log::info("Fixed unmarked bed: Bed {$bed->id} marked as occupied.");
            }

            // Fix students with bed_id but bed doesn't reference them
            $students = Student::whereNotNull('bed_id')->get();
            
            foreach ($students as $student) {
                $bed = Bed::find($student->bed_id);
                
                if (!$bed) {
                    $student->update(['bed_id' => null]);
                    $fixed++;
                    Log::warning("Fixed student {$student->id}: bed_id pointed to non-existent bed.");
                    continue;
                }

                if ($bed->student_id != $student->id) {
                    $bed->update([
                        'student_id' => $student->id,
                        'is_occupied' => true,
                    ]);
                    $fixed++;
                    Log::info("Fixed bed {$bed->id}: Updated student_id to match student {$student->id}.");
                }
            }
        });

        return ['fixed' => $fixed, 'errors' => $errors];
    }
}
