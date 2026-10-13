<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()
            ->where('name', 'admin')
            ->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@minigrove.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'ADMIN',
                'password' => Hash::make(
                    'Admin@12345'
                ),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
