<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'model' => $this->faker->randomElement(['Sedan', 'SUV', 'Hatchback', 'Coupe']),
            'year' => $this->faker->numberBetween(2000, 2024),
            'car_type' => $this->faker->randomElement(['Electric', 'Gasoline', 'Hybrid']),
            'daily_rent_price' => $this->faker->randomFloat(2, 50, 500),
            'availability' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(640, 480, 'cars', true),
        ];
    }
}
