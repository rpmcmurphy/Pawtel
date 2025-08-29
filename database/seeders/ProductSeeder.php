<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $dryFoodCategory = ProductCategory::where('slug', 'dry-food')->first();
        $wetFoodCategory = ProductCategory::where('slug', 'wet-food')->first();
        $toysCategory = ProductCategory::where('slug', 'interactive-toys')->first();
        $healthcareCategory = ProductCategory::where('slug', 'vitamins-supplements')->first();
        $groomingCategory = ProductCategory::where('slug', 'brushes-combs')->first();

        $products = [
            // Dry Food
            [
                'category_id' => $dryFoodCategory->id,
                'name' => 'Royal Canin Adult Cat Food',
                'slug' => 'royal-canin-adult-cat-food',
                'sku' => 'RC-ADULT-2KG',
                'description' => 'Complete and balanced nutrition for adult cats aged 1-7 years.',
                'price' => 2500.00,
                'compare_price' => 2800.00,
                'stock_quantity' => 50,
                'specifications' => [
                    'Weight' => '2kg',
                    'Age' => 'Adult (1-7 years)',
                    'Flavor' => 'Chicken',
                    'Special Features' => 'Supports urinary health'
                ],
                'featured' => true,
            ],
            [
                'category_id' => $dryFoodCategory->id,
                'name' => 'Whiskas Adult Dry Food',
                'slug' => 'whiskas-adult-dry-food',
                'sku' => 'WH-ADULT-1KG',
                'description' => 'Nutritious dry food with real fish and vegetables for adult cats.',
                'price' => 1200.00,
                'stock_quantity' => 75,
                'specifications' => [
                    'Weight' => '1kg',
                    'Age' => 'Adult',
                    'Flavor' => 'Tuna & Vegetables',
                ],
            ],

            // Wet Food
            [
                'category_id' => $wetFoodCategory->id,
                'name' => 'Felix As Good As It Looks',
                'slug' => 'felix-as-good-as-it-looks',
                'sku' => 'FX-AGAIL-85G',
                'description' => 'Delicious chunks in jelly with real meat and fish.',
                'price' => 150.00,
                'stock_quantity' => 100,
                'specifications' => [
                    'Weight' => '85g',
                    'Type' => 'Chunks in Jelly',
                    'Varieties' => 'Beef, Chicken, Fish',
                ],
                'featured' => true,
            ],

            // Toys
            [
                'category_id' => $toysCategory->id,
                'name' => 'Interactive Feather Wand',
                'slug' => 'interactive-feather-wand',
                'sku' => 'TOY-FW-001',
                'description' => 'Engaging feather wand toy to stimulate your cat\'s hunting instincts.',
                'price' => 800.00,
                'compare_price' => 1000.00,
                'stock_quantity' => 30,
                'specifications' => [
                    'Length' => '60cm',
                    'Material' => 'Natural feathers',
                    'Features' => 'Extendable wand',
                ],
            ],
            [
                'category_id' => $toysCategory->id,
                'name' => 'Automatic Laser Pointer',
                'slug' => 'automatic-laser-pointer',
                'sku' => 'TOY-ALP-001',
                'description' => 'Automatic laser toy that keeps your cat entertained for hours.',
                'price' => 2500.00,
                'stock_quantity' => 15,
                'specifications' => [
                    'Power' => 'USB Rechargeable',
                    'Timer' => '15 minutes auto-stop',
                    'Range' => '360-degree rotation',
                ],
                'featured' => true,
            ],

            // Healthcare
            [
                'category_id' => $healthcareCategory->id,
                'name' => 'Cat Multivitamin Tablets',
                'slug' => 'cat-multivitamin-tablets',
                'sku' => 'HLT-MVT-60',
                'description' => 'Complete multivitamin supplement for optimal cat health.',
                'price' => 1500.00,
                'stock_quantity' => 40,
                'specifications' => [
                    'Quantity' => '60 tablets',
                    'Daily Dosage' => '1 tablet',
                    'Ingredients' => 'Vitamins A, B, C, D, E',
                ],
            ],

            // Grooming
            [
                'category_id' => $groomingCategory->id,
                'name' => 'Premium Slicker Brush',
                'slug' => 'premium-slicker-brush',
                'sku' => 'GRM-PSB-001',
                'description' => 'Professional-grade slicker brush for removing loose fur and tangles.',
                'price' => 1200.00,
                'stock_quantity' => 25,
                'specifications' => [
                    'Size' => 'Medium',
                    'Material' => 'Stainless steel pins',
                    'Handle' => 'Anti-slip ergonomic',
                ],
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
