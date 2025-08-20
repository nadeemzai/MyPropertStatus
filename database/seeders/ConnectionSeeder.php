<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Connection;

class ConnectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\Property::all() as $p){ foreach(\App\Models\Agency::inRandomOrder()->take(1)->get() as $a){ Connection::factory()->create(['property_id'=>$p->id,'agency_id'=>$a->id]); } }
    }
}
