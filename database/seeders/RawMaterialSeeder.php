<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => '22K Gold (Workshop Stock)',
                'material_type' => 'gold',
                'unit' => 'grams',
                'stock_quantity' => 250.000,
                'reorder_level' => 50.000,
                'unit_cost' => 18500.00,
                'notes' => 'Primary gold stock for workshop production.',
            ],
            [
                'name' => '18K Gold (Workshop Stock)',
                'material_type' => 'gold',
                'unit' => 'grams',
                'stock_quantity' => 120.000,
                'reorder_level' => 30.000,
                'unit_cost' => 15200.00,
            ],
            [
                'name' => 'Sterling Silver Sheet',
                'material_type' => 'silver',
                'unit' => 'grams',
                'stock_quantity' => 500.000,
                'reorder_level' => 100.000,
                'unit_cost' => 320.00,
            ],
            [
                'name' => 'CZ Stone Mix (3mm)',
                'material_type' => 'gemstone',
                'unit' => 'pieces',
                'stock_quantity' => 200.000,
                'reorder_level' => 50.000,
                'unit_cost' => 45.00,
            ],
            [
                'name' => 'Ring Claw Settings',
                'material_type' => 'finding',
                'unit' => 'pieces',
                'stock_quantity' => 75.000,
                'reorder_level' => 20.000,
                'unit_cost' => 180.00,
            ],
            [
                'name' => 'Gold Solder Hard',
                'material_type' => 'alloy',
                'unit' => 'grams',
                'stock_quantity' => 15.000,
                'reorder_level' => 5.000,
                'unit_cost' => 950.00,
            ],
        ];

        foreach ($materials as $material) {
            RawMaterial::updateOrCreate(
                ['name' => $material['name']],
                $material
            );
        }
    }
}
