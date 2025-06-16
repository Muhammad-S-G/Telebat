<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $fakerEn = fake();
        $fakerAr = fake('ar_SA');

        return [
            'store_id' => Store::inRandomOrder()->first()->id,
            'section_id' => Section::inRandomOrder()->first()->id,
            'name' => [
                'en' => $fakerEn->word(),
                'ar' => $fakerAr->realText(20),
            ],

            'description' => [
                'en' => $fakerEn->sentence(),
                'ar' => $fakerAr->realText(100),
            ],

            'price' => fake()->randomFloat(2, 1, 1000),
            'quantity' => fake()->numberBetween(1, 100),
        ];
    }
}
