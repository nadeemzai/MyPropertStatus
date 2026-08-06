<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;

test('new users can register', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('phone', '5551234567')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});
