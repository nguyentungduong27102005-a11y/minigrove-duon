<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
    RoleSeeder::class,
    AdminSeeder::class,
    CustomerSeeder::class,
    PaymentSettingSeeder::class,
    CategorySeeder::class,
    ProductSeeder::class,
    ProductCategorySeeder::class,
    StudioProductSeeder::class,
    ProductImageSeeder::class,
    VoucherSeeder::class,
    CareRuleSeeder::class,
]);
    }
}