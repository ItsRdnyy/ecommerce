<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@purefit.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => \App\Models\User::ROLE_ADMIN,
            ]
        );

        $business = \App\Models\User::firstOrCreate(
            ['email' => 'business@purefit.com'],
            [
                'name' => 'Business Owner',
                'password' => 'password',
                'role' => \App\Models\User::ROLE_BUSINESS,
            ]
        );

        \App\Models\BusinessProfile::firstOrCreate(
            ['user_id' => $business->id],
            [
                'business_name' => 'PureFit Wholesale',
                'business_address' => '123 Commerce St, City',
                'business_phone' => '09123456789',
            ]
        );
    }
}
