<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->streetName(),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['apartment', 'house', 'land', 'commercial']),
            'currency' => 'PKR',
            'price' => $this->faker->numberBetween(2_000_000, 50_000_000),
            'status' => $this->faker->randomElement(['draft', 'draft', 'published', 'published', 'archived']),
            'location' => $this->faker->city(),
            'details' => [
                'bedrooms' => $this->faker->numberBetween(1, 6),
                'bathrooms' => $this->faker->numberBetween(1, 4),
                'area_sqft' => $this->faker->numberBetween(500, 5000),
            ],
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
