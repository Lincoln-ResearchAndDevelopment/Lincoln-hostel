<?php

namespace App\Console\Commands;

use App\Models\Bed;
use App\Models\Campus;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Console\Command;

/**
 * Finds why rooms or beds show as unavailable. Read-only; hostel:repair-beds does the repair.
 *
 *   php artisan hostel:diagnose-rooms            report for every campus
 *   php artisan hostel:diagnose-rooms --campus=3 report for one campus
 */
class DiagnoseRooms extends Command
{
    protected $signature = 'hostel:diagnose-rooms {--campus= : Campus id}';

    protected $description = 'Report hostel, room and bed records that make rooms look unavailable (read-only)';

    public function handle(): int
    {
        $campuses = Campus::withoutGlobalScopes()->when($this->option('campus'), fn ($q, $id) => $q->whereKey($id))->orderBy('id')->get();

        foreach ($campuses as $campus) {
            $this->newLine();
            $this->info("=== Campus {$campus->id}: {$campus->name} ===");

            $hostels = Hostel::withoutGlobalScopes()->where('campus_id', $campus->id)->get();
            $this->line('Hostels: ' . $hostels->count() . ' (active: ' . $hostels->where('status', 'active')->count() . ')');

            foreach ($hostels as $hostel) {
                $rooms = Room::withoutGlobalScopes()->where('hostel_id', $hostel->id)->get();
                $beds = Bed::withoutGlobalScopes()->whereIn('room_id', $rooms->pluck('id'))->get();
                $bookable = $rooms->filter(fn ($r) => $r->status === 'available' && $r->occupied < $r->capacity);
                $this->line(sprintf(
                    '  [%d] %s (%s, %s): rooms %d, bookable rooms %d, beds %d, free beds %d',
                    $hostel->id, $hostel->name, $hostel->type, $hostel->status,
                    $rooms->count(), $bookable->count(), $beds->count(), $beds->where('is_occupied', false)->count()
                ));
            }
        }

        $this->newLine();
        $this->info('=== Problems that hide rooms or beds ===');
        $problems = 0;

        // 1. Rooms that belong to a different campus than their hostel (campus admins would not see them).
        $roomMismatch = Room::withoutGlobalScopes()->join('hostels', 'hostels.id', '=', 'rooms.hostel_id')
            ->whereRaw('rooms.campus_id IS NULL OR rooms.campus_id <> hostels.campus_id')
            ->get(['rooms.id', 'rooms.room_number', 'rooms.campus_id as room_campus', 'hostels.campus_id as hostel_campus', 'hostels.name as hostel']);
        $problems += $this->report('Rooms whose campus differs from their hostel', $roomMismatch, fn ($r) => "room {$r->id} ({$r->hostel} / {$r->room_number}): room campus " . ($r->room_campus ?? 'none') . ", hostel campus {$r->hostel_campus}");

        // 2. Beds that belong to a different campus than their room.
        $bedMismatch = Bed::withoutGlobalScopes()->join('rooms', 'rooms.id', '=', 'beds.room_id')
            ->whereRaw('beds.campus_id IS NULL OR beds.campus_id <> rooms.campus_id')
            ->get(['beds.id', 'beds.bed_number', 'beds.campus_id as bed_campus', 'rooms.campus_id as room_campus', 'rooms.id as room_id']);
        $problems += $this->report('Beds whose campus differs from their room', $bedMismatch, fn ($b) => "bed {$b->id} in room {$b->room_id}: bed campus " . ($b->bed_campus ?? 'none') . ", room campus " . ($b->room_campus ?? 'none'));

        // 3. Rooms with fewer bed records than their capacity.
        $short = Room::withoutGlobalScopes()->withCount(['beds' => fn ($q) => $q->withoutGlobalScopes()])->get()
            ->filter(fn ($r) => $r->beds_count < $r->capacity);
        $problems += $this->report('Rooms with fewer beds than their capacity', $short, fn ($r) => "room {$r->id} (campus {$r->campus_id}, no. {$r->room_number}): capacity {$r->capacity}, beds {$r->beds_count}");

        // 4. Rooms marked full or in maintenance although beds are free.
        $blocked = Room::withoutGlobalScopes()->whereIn('status', ['full', 'maintenance'])->get()
            ->filter(fn ($r) => $r->occupied < $r->capacity);
        $this->report('Rooms marked full/maintenance although capacity is left (set by an admin; not repaired)', $blocked, fn ($r) => "room {$r->id} (campus {$r->campus_id}, no. {$r->room_number}): status {$r->status}, occupied {$r->occupied} of {$r->capacity}");

        // 5. Counts that disagree with the beds.
        $drift = Room::withoutGlobalScopes()->get()->filter(function ($r) {
            return Bed::withoutGlobalScopes()->where('room_id', $r->id)->where('is_occupied', true)->count() !== (int) $r->occupied;
        });
        $this->report('Rooms whose occupied count differs from their occupied beds (not repaired)', $drift, fn ($r) => "room {$r->id} (campus {$r->campus_id}, no. {$r->room_number}): occupied {$r->occupied}, occupied beds " . Bed::withoutGlobalScopes()->where('room_id', $r->id)->where('is_occupied', true)->count());

        // 6. Students living in a room of another campus.
        $studentMismatch = Student::withoutGlobalScopes()->join('rooms', 'rooms.id', '=', 'students.room_id')
            ->whereRaw('students.campus_id <> rooms.campus_id')->get(['students.id', 'students.admission_number', 'students.campus_id as s', 'rooms.campus_id as r']);
        $this->report('Students placed in a room of another campus (not repaired)', $studentMismatch, fn ($s) => "student {$s->id} ({$s->admission_number}): student campus {$s->s}, room campus {$s->r}");

        if ($problems > 0) {
            $this->newLine();
            $this->warn('Nothing was changed. To create missing beds and give residents their beds, run hostel:repair-beds (preview) and then hostel:repair-beds --apply.');
        }

        return self::SUCCESS;
    }

    private function report(string $title, $rows, callable $line): int
    {
        $count = $rows->count();
        $this->line(($count ? '<fg=red>' : '<fg=green>') . "{$title}: {$count}" . '</>');
        foreach ($rows->take(15) as $row) {
            $this->line('    ' . $line($row));
        }
        if ($count > 15) {
            $this->line('    ... and ' . ($count - 15) . ' more');
        }

        return $count;
    }
}
