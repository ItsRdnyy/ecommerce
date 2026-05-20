<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;

class SimpleBusinessSeeder extends Seeder
{
    public function run()
    {
        // Create business user with plain text password (matching original format)
        $user = User::create([
            'name' => 'Demo Business',
            'email' => 'demo@business.com',
            'password' => 'password', // Plain text like original seeder
            'role' => 'business',
            'email_verified_at' => now(),
        ]);

        // Create business profile
        BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Demo Business Store',
            'business_address' => '456 Demo Street',
            'business_phone' => '0987654321',
        ]);

        $this->command->info('Demo business account created successfully!');
        $this->command->info('Email: demo@business.com');
        $this->command->info('Password: password');
    }
}
