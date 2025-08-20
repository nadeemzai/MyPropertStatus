<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        foreach(\App\Models\User::all() as $u){ Notification::factory()->count(2)->create(['user_id'=>$u->id]); }
    }
}
