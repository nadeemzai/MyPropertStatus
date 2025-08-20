<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\User::all() as $u){ Property::factory()->count(3)->create(['user_id'=>$u->id]); }
    }
}
