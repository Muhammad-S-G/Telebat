<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            SectionSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            CurrencySeeder::class,
        ]);
    }
}
