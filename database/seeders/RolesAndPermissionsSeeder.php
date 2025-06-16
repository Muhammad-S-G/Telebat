<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $vendorRole = Role::create(['name' => 'vendor']);
        $userRole = Role::create(['name' => 'user']);


        $permissions = [
            'manage sections',
            'manage stores',
            'manage products',
            'view favorites',
            'manage cart',
            'create orders',
            'manage orders',
            'process payments',
            'view notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->givePermissionTo(Permission::all());
        $vendorRole->givePermissionTo(['manage stores', 'manage products', 'manage orders']);
        $userRole->givePermissionTo(['view favorites', 'manage cart', 'create orders', 'process payments', 'view notifications']);
    }
}
