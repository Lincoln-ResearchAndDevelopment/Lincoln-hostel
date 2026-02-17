<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class UpdateRoomPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all rooms to the new standard pricing
        // Semester: 85,000
        // Full Year: 250,000
        
        $count = Room::count();
        $this->command->info("Updating prices for {$count} rooms...");

        Room::query()->update([
            'price_per_semester' => 85000,
            'price_per_year' => 250000,
        ]);

        $this->command->info('All room prices updated successfully.');
    }
}
