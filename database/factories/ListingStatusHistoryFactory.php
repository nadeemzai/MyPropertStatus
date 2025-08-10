<?php

namespace Database\Factories;

use App\Models\ListingStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingStatusHistoryFactory extends Factory
{
    protected $model = ListingStatusHistory::class;

    public function definition(): array
    {
        return [
            'listing_id' => \App\Models\Listing::factory(),
            'to_status' => 'available'
        ];
    }
}
