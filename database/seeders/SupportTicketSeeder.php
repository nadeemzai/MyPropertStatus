<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\User::all() as $u){ SupportTicket::factory()->count(1)->create(['user_id'=>$u->id]); }
    }
}
