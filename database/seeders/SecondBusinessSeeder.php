<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecondBusinessSeeder extends Seeder
{
    public function run()
    {
        // Create second business user
        $user = User::create([
            'name' => 'Urban Fashion Co',
            'email' => 'urban@fashion.com',
            'password' => 'password',
            'role' => 'business',
            'email_verified_at' => now(),
        ]);

        // Create business profile
        BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Urban Fashion Co',
            'business_address' => '789 Trendy Street, Metro City',
            'business_phone' => '09987654321',
            'tax_id' => '987-654-321',
        ]);

        $this->command->info('Second business account created successfully!');
        $this->command->info('Email: urban@fashion.com');
        $this->command->info('Password: password');
    }
}
