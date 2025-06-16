<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'total' => fake()->randomFloat(2, 1, 10000),
            'store_id' => fake()->numberBetween(1, 30)
        ];
    }
}
