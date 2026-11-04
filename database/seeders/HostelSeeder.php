<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hostel;

class HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = [
            [
                'name' => 'Boys Hostel',
                'code' => 'BHS',
                'type' => 'male',
                'address' => 'Army Estate close 4',
                'description' => 'Modern hostel facility for male students with excellent amenities and security.',
                'status' => 'active',
            ],
            [
                'name' => 'Girls Hostel',
                'code' => 'GHS',
                'type' => 'female',
                'address' => 'Police Estate',
                'description' => 'Comfortable and secure hostel for female students with 24/7 security.',
                'status' => 'active',
            ],
            [
                'name' => 'East Wing Hostel',
                'code' => 'EWH',
                'type' => 'female',
                'address' => 'Army Estate Phase 1',
                'description' => 'Modern facility with separate wings.',
                'status' => 'active',
            ],
            [
                'name' => 'West Wing Annex',
                'code' => 'WWA',
                'type' => 'female',
                'address' => 'Army Estate Phase 2',
                'description' => 'Additional hostel facility for overflow accommodation.',
                'status' => 'active',
            ],
        ];

        foreach ($hostels as $hostel) {
            Hostel::create($hostel + ['campus_id' => \App\Models\Campus::orderBy('id')->value('id')]);
        }

        $this->command->info('Hostels created successfully!');
    }
}
