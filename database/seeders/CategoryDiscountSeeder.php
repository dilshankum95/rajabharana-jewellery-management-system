<?php

namespace Database\Seeders;

use App\Models\CategoryDiscount;
use Illuminate\Database\Seeder;

class CategoryDiscountSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(config('jewellery.catalog_categories', [])) as $code) {
            CategoryDiscount::query()->firstOrCreate(
                ['category_code' => $code],
                ['discount_percent' => 0, 'is_active' => true]
            );
        }
    }
}
