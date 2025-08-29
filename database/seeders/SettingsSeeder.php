<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Site Information
            ['group' => 'site', 'key' => 'name', 'value' => 'Pawtel', 'type' => 'string'],
            ['group' => 'site', 'key' => 'tagline', 'value' => 'Your Pet Care Partner', 'type' => 'string'],
            ['group' => 'site', 'key' => 'description', 'value' => 'Professional cat hotel, spa, and healthcare services with premium facilities and expert care.', 'type' => 'string'],
            ['group' => 'site', 'key' => 'phone', 'value' => '+8801234567890', 'type' => 'string'],
            ['group' => 'site', 'key' => 'email', 'value' => 'info@furbabiessafety.com', 'type' => 'string'],
            ['group' => 'site', 'key' => 'address', 'value' => 'SonÄrgaon, Dhaka Division, Bangladesh', 'type' => 'string'],

            // Business Hours
            ['group' => 'business', 'key' => 'hours', 'value' => json_encode([
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '10:00', 'close' => '16:00'],
                'sunday' => ['open' => '10:00', 'close' => '16:00'],
            ]), 'type' => 'json'],

            // Booking Settings
            ['group' => 'booking', 'key' => 'minimum_days', 'value' => '3', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'maximum_days', 'value' => '365', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'advance_booking_days', 'value' => '90', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'required_documents', 'value' => '2', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'cancellation_hours', 'value' => '24', 'type' => 'integer'],

            // E-commerce Settings
            ['group' => 'shop', 'key' => 'delivery_charge', 'value' => '50.00', 'type' => 'string'],
            ['group' => 'shop', 'key' => 'free_delivery_threshold', 'value' => '2000.00', 'type' => 'string'],
            ['group' => 'shop', 'key' => 'cod_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'shop', 'key' => 'max_order_items', 'value' => '50', 'type' => 'integer'],

            // Email Settings
            ['group' => 'email', 'key' => 'from_name', 'value' => 'Pawtel Team', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_address', 'value' => 'noreply@furbabiessafety.com', 'type' => 'string'],
            ['group' => 'email', 'key' => 'admin_email', 'value' => 'admin@furbabiessafety.com', 'type' => 'string'],

            // Social Media
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://facebook.com/pawtel', 'type' => 'string'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/pawtel', 'type' => 'string'],
            ['group' => 'social', 'key' => 'whatsapp', 'value' => '+8801234567890', 'type' => 'string'],

            // App Settings
            ['group' => 'app', 'key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'app', 'key' => 'registration_enabled', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'app', 'key' => 'max_file_size_mb', 'value' => '5', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
