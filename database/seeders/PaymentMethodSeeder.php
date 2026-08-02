<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['code' => 'cash', 'label' => 'Cash', 'requires_reference' => false, 'sort_order' => 1],
            ['code' => 'card', 'label' => 'Card', 'requires_reference' => true, 'sort_order' => 2],
            ['code' => 'bank_transfer', 'label' => 'Bank Transfer', 'requires_reference' => true, 'sort_order' => 3],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
