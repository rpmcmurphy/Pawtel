<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cat Food',
                'slug' => 'cat-food',
                'parent_id' => null,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Dry Food', 'slug' => 'dry-food'],
                    ['name' => 'Wet Food', 'slug' => 'wet-food'],
                    ['name' => 'Treats & Snacks', 'slug' => 'treats-snacks'],
                    ['name' => 'Special Diet', 'slug' => 'special-diet'],
                ]
            ],
            [
                'name' => 'Cat Toys',
                'slug' => 'cat-toys',
                'parent_id' => null,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Interactive Toys', 'slug' => 'interactive-toys'],
                    ['name' => 'Catnip Toys', 'slug' => 'catnip-toys'],
                    ['name' => 'Scratching Posts', 'slug' => 'scratching-posts'],
                    ['name' => 'Ball Toys', 'slug' => 'ball-toys'],
                ]
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'parent_id' => null,
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Vitamins & Supplements', 'slug' => 'vitamins-supplements'],
                    ['name' => 'Flea & Tick Control', 'slug' => 'flea-tick-control'],
                    ['name' => 'Dental Care', 'slug' => 'dental-care'],
                    ['name' => 'First Aid', 'slug' => 'first-aid'],
                ]
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'parent_id' => null,
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Collars & Leashes', 'slug' => 'collars-leashes'],
                    ['name' => 'Carriers & Crates', 'slug' => 'carriers-crates'],
                    ['name' => 'Bedding & Furniture', 'slug' => 'bedding-furniture'],
                    ['name' => 'Feeding Accessories', 'slug' => 'feeding-accessories'],
                ]
            ],
            [
                'name' => 'Grooming',
                'slug' => 'grooming',
                'parent_id' => null,
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Brushes & Combs', 'slug' => 'brushes-combs'],
                    ['name' => 'Shampoos & Conditioners', 'slug' => 'shampoos-conditioners'],
                    ['name' => 'Nail Care', 'slug' => 'nail-care'],
                    ['name' => 'Ear & Eye Care', 'slug' => 'ear-eye-care'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = ProductCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childData) {
                ProductCategory::firstOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, [
                        'parent_id' => $category->id,
                        'sort_order' => 0
                    ])
                );
            }
        }
    }
}
