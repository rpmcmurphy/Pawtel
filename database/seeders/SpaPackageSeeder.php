<?php

namespace Database\Seeders;

use App\Models\SpaPackage;
use Illuminate\Database\Seeder;

class SpaPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Bath & Brush',
                'slug' => 'basic-bath-brush',
                'description' => 'Essential grooming with gentle bath and thorough brushing.',
                'duration_minutes' => 60,
                'price' => 1500.00,
                'max_daily_bookings' => 8,
                'sort_order' => 1,
            ],
            [
                'name' => 'Premium Spa Package',
                'slug' => 'premium-spa',
                'description' => 'Complete spa experience with bath, grooming, nail trim, and ear cleaning.',
                'duration_minutes' => 90,
                'price' => 2500.00,
                'max_daily_bookings' => 6,
                'sort_order' => 2,
            ],
            [
                'name' => 'Full Grooming Service',
                'slug' => 'full-grooming',
                'description' => 'Comprehensive grooming including haircut, styling, and premium treatments.',
                'duration_minutes' => 120,
                'price' => 3500.00,
                'max_daily_bookings' => 4,
                'sort_order' => 3,
            ],
            [
                'name' => 'De-shedding Treatment',
                'slug' => 'deshedding-treatment',
                'description' => 'Specialized treatment to reduce shedding with professional tools and techniques.',
                'duration_minutes' => 75,
                'price' => 2000.00,
                'max_daily_bookings' => 6,
                'sort_order' => 4,
            ],
            [
                'name' => 'Luxury Spa Experience',
                'slug' => 'luxury-spa',
                'description' => 'Ultimate pampering with massage, aromatherapy, and premium grooming.',
                'duration_minutes' => 150,
                'price' => 5000.00,
                'max_daily_bookings' => 3,
                'sort_order' => 5,
            ],
            [
                'name' => 'Quick Refresh',
                'slug' => 'quick-refresh',
                'description' => 'Fast grooming session for maintenance between full services.',
                'duration_minutes' => 30,
                'price' => 800.00,
                'max_daily_bookings' => 10,
                'sort_order' => 6,
            ],
        ];

        foreach ($packages as $package) {
            SpaPackage::firstOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
