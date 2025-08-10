<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        return [
            'property_id' => \App\Models\Property::factory(),
            'agency_id' => \App\Models\Agency::factory(),
            'status' => 'pending'
        ];
    }
}
