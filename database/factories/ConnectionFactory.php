<?php

namespace Database\Factories;

use App\Models\Connection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    public function definition(): array
    {
        return [
            'agency_id' => \App\Models\Agency::factory(),
            'property_id' => \App\Models\Property::factory(),
            'status' => 'pending'
        ];
    }
}
