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
                'name' => 'Girls Hostel',
                'code' => 'GHS',
                'type' => 'female',
                'address' => 'Army Estate Phase 1',
                'description' => 'Mixed hostel with separate floors for male and female students.',
                'status' => 'active',
            ],
            [
                'name' => 'Girls Hostel',
                'code' => 'GHS',
                'type' => 'female',
                'address' => 'Army Estate Phase 2',
                'description' => 'Additional hostel facility for overflow accommodation.',
                'status' => 'maintenance',
            ],
        ];

        foreach ($hostels as $hostel) {
            Hostel::create($hostel);
        }

        $this->command->info('Hostels created successfully!');
    }
}
