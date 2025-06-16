<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    public function definition(): array
    {
        $fakerEn = fake();
        $fakerAr = fake('ar_SA');

        return [
            'name' => [
                'en' => $fakerEn->word(),
                'ar' => $fakerAr->realText(20),
            ],

            'description' => [
                'en' => $fakerEn->sentence(),
                'ar' => $fakerAr->realText(100),
            ],
        ];
    }
}
