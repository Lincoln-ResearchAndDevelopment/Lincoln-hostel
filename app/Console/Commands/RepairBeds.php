<?php

namespace App\Console\Commands;

use App\Models\Bed;
use App\Models\Campus;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Brings rooms that pre-date bed records up to date:
 *
 *  1. creates the missing beds, up to each room's capacity
 *  2. gives students who live in a room but have no bed a bed of their own (in student order)
 *  3. sets rooms marked "full" back to "available" when they still have space
 *
 * It only shows what it would do unless --apply is given.
 *
 *   php artisan hostel:repair-beds            preview, nothing is saved
 *   php artisan hostel:repair-beds --apply    do it
 *   php artisan hostel:repair-beds --campus=1 limit to one campus
 */
class RepairBeds extends Command
{
    protected $signature = 'hostel:repair-beds {--apply : Save the changes (otherwise only a preview is shown)} {--campus= : Campus id}';

    protected $description = 'Create missing beds, give residents their beds, and reopen rooms wrongly marked full';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'APPLYING changes.' : 'PREVIEW only. Nothing will be saved; add --apply to save.');

        $created = 0;
        $placed = 0;
        $reopened = [];
        $unplaced = [];
        $perCampus = [];

        DB::beginTransaction();
        try {
            $rooms = Room::withoutGlobalScopes()
                ->when($this->option('campus'), fn ($q, $id) => $q->where('campus_id', $id))
                ->orderBy('campus_id')->orderBy('hostel_id')->orderBy('id')->get();

            foreach ($rooms as $room) {
                $campusKey = $room->campus_id;
                $perCampus[$campusKey] ??= ['rooms' => 0, 'created' => 0, 'placed' => 0];

                // 1. Missing beds.
                $labels = Bed::withoutGlobalScopes()->where('room_id', $room->id)->pluck('bed_number')->all();
                $count = count($labels);
                $n = 1;
                while ($count < $room->capacity) {
                    $label = 'Bed ' . $n++;
                    if (in_array($label, $labels, true)) {
                        continue;
                    }
                    Bed::withoutGlobalScopes()->create([
                        'room_id' => $room->id,
                        'campus_id' => $room->campus_id,
                        'bed_number' => $label,
                        'is_occupied' => false,
                    ]);
                    $labels[] = $label;
                    $count++;
                    $created++;
                    $perCampus[$campusKey]['created']++;
                }

                // 2. Residents without a bed.
                $residents = Student::withoutGlobalScopes()->where('room_id', $room->id)->orderBy('id')->get();
                foreach ($residents as $student) {
                    $hasBed = $student->bed_id && Bed::withoutGlobalScopes()
                        ->where('id', $student->bed_id)->where('room_id', $room->id)->where('student_id', $student->id)->exists();
                    if ($hasBed) {
                        continue;
                    }

                    $bed = Bed::withoutGlobalScopes()->where('room_id', $room->id)->where('is_occupied', false)->orderBy('id')->lockForUpdate()->first();
                    if (!$bed) {
                        $unplaced[] = "student {$student->id} ({$student->admission_number}) in room {$room->id} ({$room->room_number}, campus {$room->campus_id}): no free bed, room is over capacity";
                        continue;
                    }

                    Bed::withoutGlobalScopes()->whereKey($bed->id)->update(['is_occupied' => true, 'student_id' => $student->id]);
                    Student::withoutGlobalScopes()->whereKey($student->id)->update(['bed_id' => $bed->id]);
                    $placed++;
                    $perCampus[$campusKey]['placed']++;
                }

                // 3. Rooms marked full that still have space.
                if ($room->status === 'full' && $room->occupied < $room->capacity) {
                    Room::withoutGlobalScopes()->whereKey($room->id)->update(['status' => 'available']);
                    $reopened[] = "room {$room->id} ({$room->room_number}, campus {$room->campus_id}): occupied {$room->occupied} of {$room->capacity}";
                }

                $perCampus[$campusKey]['rooms']++;
            }

            $apply ? DB::commit() : DB::rollBack();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed, nothing was saved: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        foreach ($perCampus as $campusId => $row) {
            $name = Campus::withoutGlobalScopes()->whereKey($campusId)->value('name') ?? 'No campus';
            $this->line("Campus {$campusId} ({$name}): rooms checked {$row['rooms']}, beds " . ($apply ? 'created' : 'to create') . " {$row['created']}, residents " . ($apply ? 'given a bed' : 'to give a bed') . " {$row['placed']}");
        }
        $this->line('Total beds ' . ($apply ? 'created' : 'to create') . ": {$created}");
        $this->line('Total residents ' . ($apply ? 'given a bed' : 'to give a bed') . ": {$placed}");

        $this->line('Rooms marked full that ' . ($apply ? 'were reopened' : 'would be reopened') . ': ' . count($reopened));
        foreach ($reopened as $line) {
            $this->line('    ' . $line);
        }

        if ($unplaced) {
            $this->warn('Residents that could not be given a bed (' . count($unplaced) . '):');
            foreach ($unplaced as $line) {
                $this->line('    ' . $line);
            }
        }

        $this->newLine();
        $this->line($apply ? 'Done. Run hostel:diagnose-rooms to check the result.' : 'Nothing was changed.');

        return self::SUCCESS;
    }
}
