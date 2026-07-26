<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HostelSeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hostel_seeder_creates_hostels_without_duplicate_codes()
    {
        $this->seed(\Database\Seeders\HostelSeeder::class);

        $hostels = \App\Models\Hostel::all();

        // Each hostel must have a unique code
        $codes = $hostels->pluck('code')->toArray();
        $this->assertCount(4, $codes, 'Should have exactly 4 hostels');
        $this->assertCount(4, array_unique($codes), 'All hostel codes must be unique');

        // Verify specific codes
        $this->assertContains('BHS', $codes, 'Boys Hostel code must exist');
        $this->assertContains('GHS', $codes, 'Girls Hostel (Police Estate) code must exist');
        $this->assertContains('GH1', $codes, 'Girls Hostel Phase 1 code must exist');
        $this->assertContains('GH2', $codes, 'Girls Hostel Phase 2 code must exist');

        // Verify active hostels
        $activeCount = $hostels->where('status', 'active')->count();
        $this->assertEquals(3, $activeCount, 'Should have 3 active hostels');

        // Verify maintenance hostel
        $maintenanceHostel = $hostels->where('status', 'maintenance')->first();
        $this->assertNotNull($maintenanceHostel);
        $this->assertEquals('GH2', $maintenanceHostel->code);
    }
}
