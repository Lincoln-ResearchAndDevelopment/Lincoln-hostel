<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Hostels to get IDs
        $nwh = \App\Models\Hostel::where('code', 'BHS')->first();
        $swh = \App\Models\Hostel::where('code', 'GHS')->first();
        $ewh = \App\Models\Hostel::where('code', 'EWH')->first();
        $wwa = \App\Models\Hostel::where('code', 'WWA')->first();

        $rooms = [
            // Boys Hostel (BHS)
            ['hostel_id' => $nwh->id, 'room_number' => 'BHS-101', 'room_type' => 'single', 'capacity' => 1, 'price' => 1500],
            ['hostel_id' => $nwh->id, 'room_number' => 'BHS-102', 'room_type' => 'double', 'capacity' => 2, 'price' => 1200],
            ['hostel_id' => $nwh->id, 'room_number' => 'BHS-103', 'room_type' => 'double', 'capacity' => 2, 'price' => 1200],
            ['hostel_id' => $nwh->id, 'room_number' => 'BHS-104', 'room_type' => 'triple', 'capacity' => 3, 'price' => 1000],
            ['hostel_id' => $nwh->id, 'room_number' => 'BHS-105', 'room_type' => 'quad',   'capacity' => 4, 'price' => 800],
            
            // Girls Hostel (GHS)
            ['hostel_id' => $swh->id, 'room_number' => 'GHS-101', 'room_type' => 'single', 'capacity' => 1, 'price' => 1500],
            ['hostel_id' => $swh->id, 'room_number' => 'GHS-102', 'room_type' => 'double', 'capacity' => 2, 'price' => 1200],
            ['hostel_id' => $swh->id, 'room_number' => 'GHS-103', 'room_type' => 'double', 'capacity' => 2, 'price' => 1200],
            ['hostel_id' => $swh->id, 'room_number' => 'GHS-104', 'room_type' => 'triple', 'capacity' => 3, 'price' => 1000],
            ['hostel_id' => $swh->id, 'room_number' => 'GHS-105', 'room_type' => 'quad',   'capacity' => 4, 'price' => 800],

            // East Wing Hostel (Mixed)
            ['hostel_id' => $ewh->id, 'room_number' => 'EWH-101', 'room_type' => 'single', 'capacity' => 1, 'price' => 1500],
            ['hostel_id' => $ewh->id, 'room_number' => 'EWH-102', 'room_type' => 'double', 'capacity' => 2, 'price' => 1200],
            ['hostel_id' => $ewh->id, 'room_number' => 'EWH-103', 'room_type' => 'triple', 'capacity' => 3, 'price' => 1000],

             // West Wing Annex (Overflow)
            ['hostel_id' => $wwa->id, 'room_number' => 'WWA-101', 'room_type' => 'quad', 'capacity' => 4, 'price' => 750],
            ['hostel_id' => $wwa->id, 'room_number' => 'WWA-102', 'room_type' => 'quad', 'capacity' => 4, 'price' => 750],
        ];

        foreach ($rooms as $room) {
            \App\Models\Room::create([
                'hostel_id' => $room['hostel_id'],
                'room_number' => $room['room_number'],
                'room_type' => $room['room_type'],
                'capacity' => $room['capacity'],
                'occupied' => 0,
                'price_per_semester' => $room['price'],
                'price_per_year' => $room['price'] * 2,
                'floor_number' => 1,
                'status' => 'available',
                'description' => "Standard " . ucfirst($room['room_type']) . " Room",
                'facilities' => ['WiFi', 'Fan', 'Desk', 'Wardrobe'],
            ]);
        }    }
}
