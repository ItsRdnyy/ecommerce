<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestBusinessSeeder extends Seeder
{
    public function run()
    {
        // Create test business user with simple password
        $user = User::create([
            'name' => 'Test Business',
            'email' => 'test@business.com',
            'password' => '123456',
            'role' => 'business',
            'email_verified_at' => now(),
        ]);

        // Create business profile
        BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business Store',
            'business_address' => '123 Test Street',
            'business_phone' => '1234567890',
            'tax_id' => 'TEST123',
        ]);

        $this->command->info('Test business account created successfully!');
        $this->command->info('Email: test@business.com');
        $this->command->info('Password: 123456');
    }
}
