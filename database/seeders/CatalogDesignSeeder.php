<?php

namespace Database\Seeders;

use App\Enums\AvailabilityStatus;
use App\Models\CatalogDesign;
use Illuminate\Database\Seeder;

class CatalogDesignSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'code' => 'CAT-R001',
                'name' => 'Classic Temple Ring',
                'description' => 'Traditional Sri Lankan temple-inspired ring with intricate filigree work.',
                'category' => 'ring',
                'gold_quality' => '22k',
                'weight_grams' => 8.5,
                'selling_price' => 185000,
                'availability_status' => AvailabilityStatus::Available,
            ],
            [
                'code' => 'CAT-P001',
                'name' => 'Royal Pendant Set',
                'description' => 'Elegant pendant with matching ear studs, perfect for special occasions.',
                'category' => 'pendant',
                'gold_quality' => '22k',
                'weight_grams' => 15.0,
                'selling_price' => 320000,
                'availability_status' => AvailabilityStatus::Available,
            ],
            [
                'code' => 'CAT-B001',
                'name' => 'Heritage Bangle',
                'description' => 'Handcrafted bangle featuring traditional Rajabharana motifs.',
                'category' => 'bangle',
                'gold_quality' => '22k',
                'weight_grams' => 25.0,
                'selling_price' => 450000,
                'availability_status' => AvailabilityStatus::Available,
            ],
            [
                'code' => 'CAT-N001',
                'name' => 'Bridal Necklace',
                'description' => 'Grand bridal necklace with ruby and emerald accents.',
                'category' => 'necklace',
                'gold_quality' => '22k',
                'weight_grams' => 45.0,
                'selling_price' => 890000,
                'availability_status' => AvailabilityStatus::Available,
            ],
            [
                'code' => 'CAT-C001',
                'name' => 'Minimalist Chain',
                'description' => 'Sleek daily-wear chain available in multiple lengths.',
                'category' => 'chain',
                'gold_quality' => '18k',
                'weight_grams' => 6.0,
                'selling_price' => 95000,
                'availability_status' => AvailabilityStatus::Available,
            ],
            [
                'code' => 'CAT-E001',
                'name' => 'Floral Earrings',
                'description' => 'Delicate floral drop earrings with pearl detailing.',
                'category' => 'earring',
                'gold_quality' => '18k',
                'weight_grams' => 4.5,
                'selling_price' => 78000,
                'availability_status' => AvailabilityStatus::OutOfStock,
            ],
        ];

        foreach ($items as $item) {
            CatalogDesign::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
