<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        $fakerEn = fake();
        $fakerAr = fake('ar_SA');

        return [
            'section_id' => Section::inRandomOrder()->first()->id,
            'vendor_id' => User::role('vendor')->first()->id,
            'name' => [
                'en' => $fakerEn->word(),
                'ar' => $fakerAr->realText(20),
            ],
        ];
    }
}
