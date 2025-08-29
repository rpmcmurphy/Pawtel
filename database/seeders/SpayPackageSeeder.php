<?php

namespace Database\Seeders;

use App\Models\SpayPackage;
use Illuminate\Database\Seeder;

class SpayPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            // Spay packages
            [
                'name' => 'Basic Spay',
                'type' => 'spay',
                'description' => 'Standard spaying procedure with post-operative monitoring.',
                'price' => 8000.00,
                'post_care_days' => 7,
                'max_daily_slots' => 2,
            ],
            [
                'name' => 'Premium Spay',
                'type' => 'spay',
                'description' => 'Spaying with advanced anesthesia and extended post-operative care.',
                'price' => 12000.00,
                'post_care_days' => 10,
                'max_daily_slots' => 2,
            ],
            [
                'name' => 'Comprehensive Spay Package',
                'type' => 'spay',
                'description' => 'Complete spaying package including pre-op tests, surgery, and full recovery care.',
                'price' => 15000.00,
                'post_care_days' => 14,
                'max_daily_slots' => 1,
            ],

            // Neuter packages
            [
                'name' => 'Basic Neuter',
                'type' => 'neuter',
                'description' => 'Standard neutering procedure with post-operative monitoring.',
                'price' => 6000.00,
                'post_care_days' => 5,
                'max_daily_slots' => 3,
            ],
            [
                'name' => 'Premium Neuter',
                'type' => 'neuter',
                'description' => 'Neutering with advanced anesthesia and extended post-operative care.',
                'price' => 9000.00,
                'post_care_days' => 7,
                'max_daily_slots' => 2,
            ],
            [
                'name' => 'Comprehensive Neuter Package',
                'type' => 'neuter',
                'description' => 'Complete neutering package including pre-op tests, surgery, and full recovery care.',
                'price' => 12000.00,
                'post_care_days' => 10,
                'max_daily_slots' => 2,
            ],
        ];

        foreach ($packages as $package) {
            SpayPackage::firstOrCreate(
                ['name' => $package['name'], 'type' => $package['type']],
                $package
            );
        }
    }
}
