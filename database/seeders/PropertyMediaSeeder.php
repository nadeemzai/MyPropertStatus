<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyMedia;

class PropertyMediaSeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\Property::all() as $p){ PropertyMedia::factory()->count(2)->create(['property_id'=>$p->id]); }
    }
}
