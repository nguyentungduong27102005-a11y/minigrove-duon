<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::query()
            ->where('name', 'customer')
            ->firstOrFail();

        User::firstOrCreate(
            ['email' => 'customer@minigrove.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'CUSTOMER',
                'password' => Hash::make('Customer@12345'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
