<?php

use App\Livewire\Dashboard\Notifications;
use App\Models\Connection;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.notifications.index'))->assertRedirect(route('login'));
});

test('only the authenticated user\'s own notifications are listed', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Notification::factory()->create(['user_id' => $user->id, 'title' => 'My notification']);
    Notification::factory()->create(['user_id' => $other->id, 'title' => 'Their notification']);

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->assertSee('My notification')
        ->assertDontSee('Their notification');
});

test('a listing_proposed notification for a still-pending listing shows accept/decline', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);
    Notification::factory()->create([
        'user_id' => $owner->id,
        'payload' => ['type' => 'listing_proposed', 'listing_id' => $listing->id],
    ]);

    Livewire::actingAs($owner)
        ->test(Notifications::class)
        ->assertSee('Accept')
        ->assertSee('Decline');
});

test('accepting a listing_proposed notification approves the listing and marks it read', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);
    $notification = Notification::factory()->create([
        'user_id' => $owner->id,
        'payload' => ['type' => 'listing_proposed', 'listing_id' => $listing->id],
    ]);

    Livewire::actingAs($owner)
        ->test(Notifications::class)
        ->call('accept', $notification->id);

    expect($listing->fresh()->status)->toBe('available');
    expect($notification->fresh()->is_read)->toBeTrue();
});

test('declining a connection_request notification rejects the connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);
    $notification = Notification::factory()->create([
        'user_id' => $owner->id,
        'payload' => ['type' => 'connection_request', 'connection_id' => $connection->id],
    ]);

    Livewire::actingAs($owner)
        ->test(Notifications::class)
        ->call('decline', $notification->id);

    expect($connection->fresh()->status)->toBe('rejected');
    expect($notification->fresh()->is_read)->toBeTrue();
});

test('acting on an already-resolved notification shows an error', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);
    $notification = Notification::factory()->create([
        'user_id' => $owner->id,
        'payload' => ['type' => 'listing_proposed', 'listing_id' => $listing->id],
    ]);

    $component = Livewire::actingAs($owner)->test(Notifications::class);
    $component->call('accept', $notification->id);
    $component->call('decline', $notification->id);

    expect($component->get('error'))->not->toBeNull();
});

test('a resolved listing_proposed notification no longer shows accept/decline', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);
    Notification::factory()->create([
        'user_id' => $owner->id,
        'payload' => ['type' => 'listing_proposed', 'listing_id' => $listing->id],
    ]);

    Livewire::actingAs($owner)
        ->test(Notifications::class)
        ->assertDontSee('Accept')
        ->assertDontSee('Decline');
});

test('a non-actionable notification can be marked as read', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->assertSee('Mark as read')
        ->call('markRead', $notification->id);

    expect($notification->fresh()->is_read)->toBeTrue();
});

test('a user cannot mark another user\'s notification as read', function () {
    $user = User::factory()->create();
    $intruder = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    $this->actingAs($intruder);

    expect(fn () => Livewire::test(Notifications::class)->call('markRead', $notification->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
