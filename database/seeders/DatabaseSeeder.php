<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SuperAdminSeeder::class,
            AgencySeeder::class,
            PropertySeeder::class,
            ListingSeeder::class,
            ListingStatusHistorySeeder::class,
            ConnectionSeeder::class,
            NotificationSeeder::class,
            SupportTicketSeeder::class,
            PropertyMediaSeeder::class,
            AgencyApiKeySeeder::class,
        ]);
    }
}
