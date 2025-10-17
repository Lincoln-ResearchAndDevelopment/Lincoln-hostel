<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the master super admin (first and only master admin)
        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@linchostel.com'],
            [
                'name' => 'Master Super Admin',
                'password' => Hash::make('SuperAdmin@2025'),
                'phone' => '+1-555-0123',
                'bio' => 'Master administrator with full system access and control.',
                'is_active' => true,
                'is_master' => true,
                'permissions' => ['*'], // All permissions
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Master Super Admin created successfully!');
        $this->command->info('Email: superadmin@linchostel.com');
        $this->command->info('Password: SuperAdmin@2025');
        $this->command->info('Note: Only one Master Super Admin is allowed in the system.');
    }
}
