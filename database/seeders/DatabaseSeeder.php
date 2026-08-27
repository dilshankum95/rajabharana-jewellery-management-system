<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'customer@rajabharana.com'],
            [
                'name' => 'Test Customer',
                'phone' => '0771234567',
                'address' => '123 Galle Road, Colombo 03',
                'city' => 'Colombo',
                'role' => 'customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@rajabharana.com'],
            [
                'name' => 'Admin User',
                'phone' => '0779876543',
                'address' => '456 Kandy Road, Rajagiriya',
                'city' => 'Colombo',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@rajabharana.com'],
            [
                'name' => 'Store Manager',
                'phone' => '0771111111',
                'address' => '789 Main Street, Kandy',
                'city' => 'Kandy',
                'role' => 'manager',
                'password' => Hash::make('Password1'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@rajabharana.com'],
            [
                'name' => 'Sales Staff',
                'phone' => '0772222222',
                'address' => '321 Lake Road, Negombo',
                'city' => 'Negombo',
                'role' => 'staff',
                'password' => Hash::make('Password1'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'technician@rajabharana.com'],
            [
                'name' => 'Workshop Technician',
                'phone' => '0773333333',
                'address' => 'Workshop, Rajagiriya',
                'city' => 'Colombo',
                'role' => 'technician',
                'password' => Hash::make('Password1'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(CatalogDesignSeeder::class);
        $this->call(RawMaterialSeeder::class);
        $this->call(MetalPriceSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(BillingSettingSeeder::class);
        $this->call(CategoryDiscountSeeder::class);
    }
}
