<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraph()
        ];
    }
}
