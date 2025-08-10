<?php

namespace Database\Factories;

use App\Models\AgencyApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgencyApiKeyFactory extends Factory
{
    protected $model = AgencyApiKey::class;

    public function definition(): array
    {
        return [
            'agency_id' => \App\Models\Agency::factory(),
            'api_key' => $this->faker->unique()->sha256
        ];
    }
}
