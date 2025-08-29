<?php

namespace Database\Seeders;

use App\Models\AddonService;
use Illuminate\Database\Seeder;

class AddonServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Hotel addons
            [
                'name' => 'Nail Trimming',
                'slug' => 'nail-trimming',
                'category' => 'hotel',
                'price' => 200.00,
                'description' => 'Professional nail trimming service for your cat\'s comfort and health.',
            ],
            [
                'name' => 'Basic Grooming',
                'slug' => 'basic-grooming',
                'category' => 'hotel',
                'price' => 800.00,
                'description' => 'Basic grooming including brushing, ear cleaning, and hygiene maintenance.',
            ],
            [
                'name' => 'Flea Treatment',
                'slug' => 'flea-treatment',
                'category' => 'hotel',
                'price' => 500.00,
                'description' => 'Safe and effective flea treatment to keep your cat comfortable.',
            ],
            [
                'name' => 'Health Checkup',
                'slug' => 'health-checkup',
                'category' => 'hotel',
                'price' => 1000.00,
                'description' => 'Comprehensive health checkup by our veterinary team.',
            ],

            // Spa addons
            [
                'name' => 'Premium Shampoo',
                'slug' => 'premium-shampoo',
                'category' => 'spa',
                'price' => 300.00,
                'description' => 'Organic premium shampoo treatment for sensitive skin.',
            ],
            [
                'name' => 'Aromatherapy',
                'slug' => 'aromatherapy',
                'category' => 'spa',
                'price' => 400.00,
                'description' => 'Relaxing aromatherapy session to reduce stress and anxiety.',
            ],
            [
                'name' => 'Teeth Cleaning',
                'slug' => 'teeth-cleaning',
                'category' => 'spa',
                'price' => 600.00,
                'description' => 'Professional dental cleaning for optimal oral health.',
            ],

            // General addons
            [
                'name' => 'Pick-up Service',
                'slug' => 'pickup-service',
                'category' => 'general',
                'price' => 500.00,
                'description' => 'Convenient pick-up service from your location.',
            ],
            [
                'name' => 'Drop-off Service',
                'slug' => 'dropoff-service',
                'category' => 'general',
                'price' => 500.00,
                'description' => 'Safe drop-off service to your location.',
            ],
            [
                'name' => 'Emergency Care Package',
                'slug' => 'emergency-care',
                'category' => 'general',
                'price' => 1500.00,
                'description' => '24/7 emergency care coverage during stay.',
            ],
        ];

        foreach ($services as $service) {
            AddonService::firstOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
