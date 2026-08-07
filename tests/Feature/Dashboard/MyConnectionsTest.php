<?php

use App\Livewire\Dashboard\MyConnections;
use App\Models\Agency;
use App\Models\Connection;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.connections.index'))->assertRedirect(route('login'));
});

test('only connections on the authenticated user\'s properties are shown', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id, 'title' => 'My House']);
    $otherProperty = Property::factory()->create(['user_id' => $other->id, 'title' => 'Their House']);

    Connection::factory()->create(['property_id' => $ownProperty->id]);
    Connection::factory()->create(['property_id' => $otherProperty->id]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->assertSee('My House')
        ->assertDontSee('Their House');
});

test('a pending connection shows accept and reject buttons', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    Connection::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->assertSee('Accept')
        ->assertSee('Reject');
});

test('owner can accept a connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->call('accept', $connection->id);

    expect($connection->fresh()->status)->toBe('accepted');
});

test('owner can reject a connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->call('reject', $connection->id);

    expect($connection->fresh()->status)->toBe('rejected');
});

test('a user cannot respond to a connection on another user\'s property', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    $this->actingAs($intruder);

    expect(fn () => Livewire::test(MyConnections::class)->call('accept', $connection->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect($connection->fresh()->status)->toBe('pending');
});

test('responding to an already-responded connection shows an error', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->call('accept', $connection->id)
        ->call('reject', $connection->id)
        ->assertSet('error', 'This connection request is no longer pending (status: accepted).');
});

test('agency name and message are shown for each connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create(['name' => 'Best Realtors']);
    Connection::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $agency->id,
        'message' => 'We would love to list this property.',
    ]);

    Livewire::actingAs($owner)
        ->test(MyConnections::class)
        ->assertSee('Best Realtors')
        ->assertSee('We would love to list this property.');
});
