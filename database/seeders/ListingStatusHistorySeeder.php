<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListingStatusHistory;

class ListingStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\Listing::all() as $l){ ListingStatusHistory::factory()->count(2)->create(['listing_id'=>$l->id]); }
    }
}
