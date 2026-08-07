<?php

use App\Livewire\Dashboard\SupportTickets;
use App\Models\SupportTicket;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.support.index'))->assertRedirect(route('login'));
});

test('only the authenticated user\'s own tickets are listed', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    SupportTicket::factory()->create(['user_id' => $user->id, 'subject' => 'My problem']);
    SupportTicket::factory()->create(['user_id' => $other->id, 'subject' => 'Their problem']);

    Livewire::actingAs($user)
        ->test(SupportTickets::class)
        ->assertSee('My problem')
        ->assertDontSee('Their problem');
});

test('a user can create a support ticket', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SupportTickets::class)
        ->set('subject', 'App keeps crashing')
        ->set('message', 'It crashes every time I open the listings page.')
        ->call('createTicket')
        ->assertSet('showForm', false);

    expect(SupportTicket::where('user_id', $user->id)->where('subject', 'App keeps crashing')->exists())->toBeTrue();
});

test('subject and message are required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SupportTickets::class)
        ->set('subject', '')
        ->set('message', '')
        ->call('createTicket')
        ->assertHasErrors(['subject', 'message']);
});

test('ticket status is shown', function () {
    $user = User::factory()->create();
    SupportTicket::factory()->create(['user_id' => $user->id, 'status' => 'resolved']);

    Livewire::actingAs($user)
        ->test(SupportTickets::class)
        ->assertSee('Resolved');
});
