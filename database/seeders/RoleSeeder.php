<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'customer'],
            [
                'display_name' => 'Khách hàng',
                'description' => 'Khách hàng sử dụng website MiniGrove',
                'is_active' => true,
            ]
        );
;

        Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Quản trị viên',
                'description' => 'Quản trị toàn bộ hệ thống MiniGrove',
                'is_active' => true,
            ]
        );
    }
}