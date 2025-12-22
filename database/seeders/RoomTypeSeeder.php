<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Single Room',
                'slug' => 'single-room',
                'base_daily_rate' => 500.00,
                'rate_7plus_days' => 450.00,
                'rate_10plus_days' => 400.00,
                'monthly_package_price' => 10000.00,
                'monthly_custom_discount_enabled' => true,
                'max_capacity' => 1,
                'amenities' => [
                    'Air Conditioning',
                    'Comfortable Bedding',
                    'Food & Water Bowls',
                    'Daily Cleaning',
                    'Play Time'
                ],
                'room_count' => 10,
            ],
            [
                'name' => 'Double Room',
                'slug' => 'double-room',
                'base_daily_rate' => 800.00,
                'rate_7plus_days' => 720.00,
                'rate_10plus_days' => 640.00,
                'monthly_package_price' => 16000.00,
                'monthly_custom_discount_enabled' => true,
                'max_capacity' => 2,
                'amenities' => [
                    'Air Conditioning',
                    'Spacious Area',
                    'Comfortable Bedding',
                    'Food & Water Bowls',
                    'Daily Cleaning',
                    'Extended Play Time',
                    'Socialization'
                ],
                'room_count' => 8,
            ],
            [
                'name' => 'Family Suite',
                'slug' => 'family-suite',
                'base_daily_rate' => 1200.00,
                'rate_7plus_days' => 1080.00,
                'rate_10plus_days' => 960.00,
                'monthly_package_price' => 22000.00,
                'monthly_custom_discount_enabled' => true,
                'max_capacity' => 4,
                'amenities' => [
                    'Premium Air Conditioning',
                    'Large Family Space',
                    'Premium Bedding',
                    'Multiple Food & Water Stations',
                    'Twice Daily Cleaning',
                    'Extended Play Time',
                    'Grooming Service',
                    'Daily Health Checkup'
                ],
                'room_count' => 5,
            ],
        ];

        foreach ($roomTypes as $index => $roomTypeData) {
            $roomCount = $roomTypeData['room_count'];
            unset($roomTypeData['room_count']);

            $roomType = RoomType::firstOrCreate(
                ['slug' => $roomTypeData['slug']],
                array_merge($roomTypeData, ['sort_order' => $index + 1])
            );

            // Create rooms for this room type
            for ($i = 1; $i <= $roomCount; $i++) {
                Room::firstOrCreate(
                    [
                        'room_type_id' => $roomType->id,
                        'room_number' => strtoupper(substr($roomType->slug, 0, 1)) . str_pad($i, 3, '0', STR_PAD_LEFT)
                    ],
                    [
                        'floor' => ceil($i / 5), // 5 rooms per floor
                        'status' => 'available',
                    ]
                );
            }
        }
    }
}
