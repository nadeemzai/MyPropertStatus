<?php

namespace Database\Factories;

use App\Models\PropertyMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyMediaFactory extends Factory
{
    protected $model = PropertyMedia::class;

    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::factory(),
            'path' => $this->faker->imageUrl()
        ];
    }
}
