<?php

namespace Database\Seeders;

use App\Models\BillingSetting;
use Illuminate\Database\Seeder;

class BillingSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (BillingSetting::query()->exists()) {
            return;
        }

        BillingSetting::create([
            'tax_rate_percent' => 0,
        ]);
    }
}
