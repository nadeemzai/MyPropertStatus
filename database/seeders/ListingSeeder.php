<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\Property::all() as $p){ foreach(\App\Models\Agency::inRandomOrder()->take(2)->get() as $a){ Listing::factory()->create(['property_id'=>$p->id,'agency_id'=>$a->id]); } }
    }
}
