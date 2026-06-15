<?php

namespace Database\Seeders;

use App\Models\MetalPrice;
use App\Models\User;
use Illuminate\Database\Seeder;

class MetalPriceSeeder extends Seeder
{
    public function run(): void
    {
        if (MetalPrice::query()->exists()) {
            return;
        }

        $admin = User::where('email', 'admin@rajabharana.com')->first();

        MetalPrice::create([
            'gold_price_per_gram' => 18500.00,
            'silver_price_per_gram' => 350.00,
            'price_date' => today(),
            'updated_by' => $admin?->id,
        ]);
    }
}
