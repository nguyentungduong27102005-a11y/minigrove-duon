<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductComponentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Forest Calm
        |--------------------------------------------------------------------------
        */

        $forest = Product::where(
            'sku',
            'MG-FOREST-001'
        )->firstOrFail();

        $forest->components()->sync([
            Component::where('sku', 'CP-CONTAINER-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-PLANT-001')
                ->firstOrFail()->id => [
                    'quantity' => 2,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-MOSS-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-SUBSTRATE-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-SUBSTRATE-002')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-SUBSTRATE-003')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mushroom Whisper
        |--------------------------------------------------------------------------
        */

        $mushroom = Product::where(
            'sku',
            'MG-MUSHROOM-001'
        )->firstOrFail();

        $mushroom->components()->sync([
            Component::where('sku', 'CP-CONTAINER-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-PLANT-002')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-MOSS-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-SUBSTRATE-003')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-ACCESSORY-002')
                ->firstOrFail()->id => [
                    'quantity' => 2,
                    'is_required' => false,
                ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Desert Stone
        |--------------------------------------------------------------------------
        */

        $desert = Product::where(
            'sku',
            'MG-DESERT-001'
        )->firstOrFail();

        $desert->components()->sync([
            Component::where('sku', 'CP-CONTAINER-003')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-PLANT-003')
                ->firstOrFail()->id => [
                    'quantity' => 2,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-SUBSTRATE-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => true,
                ],

            Component::where('sku', 'CP-STONE-001')
                ->firstOrFail()->id => [
                    'quantity' => 1,
                    'is_required' => false,
                ],
        ]);
    }
}