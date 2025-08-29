<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage_users',
            'manage_bookings',
            'manage_products',
            'manage_orders',
            'manage_posts',
            'manage_settings',
            'view_reports',
            'manage_rooms',
            'manage_services',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Customer role gets no special permissions by default
    }
}
