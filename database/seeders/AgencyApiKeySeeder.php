<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgencyApiKey;

class AgencyApiKeySeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\Agency::all() as $a){ AgencyApiKey::factory()->count(1)->create(['agency_id'=>$a->id]); }
    }
}
