<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Primary Admin User
        User::updateOrCreate(
            ['email' => 'admin@linchostel.com'],
            [
                'name' => 'LincHostel Admin',
                'password' => bcrypt('admin12345'),
                'role' => 'admin'
            ]
        );
    }
}
