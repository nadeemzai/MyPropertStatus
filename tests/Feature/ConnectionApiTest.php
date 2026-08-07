<?php

use App\Models\Agency;
use App\Models\AgencyApiKey;
use App\Models\Connection;
use App\Models\Notification;
use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('owner can accept a connection via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/connections/{$connection->id}/accept")
        ->assertOk()
        ->assertJsonPath('status', 'accepted');
});

test('owner can reject a connection via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/connections/{$connection->id}/reject")
        ->assertOk()
        ->assertJsonPath('status', 'rejected');
});

test('a non-owner cannot accept a connection via the api', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($intruder);

    $this->postJson("/api/connections/{$connection->id}/accept")->assertForbidden();
});

test('accepting an already-responded connection conflicts', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/connections/{$connection->id}/accept")->assertOk();
    $this->postJson("/api/connections/{$connection->id}/reject")->assertStatus(409);
});

test('a past-expiry connection cannot be accepted and is marked expired', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'expires_at' => now()->subHour(),
    ]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/connections/{$connection->id}/accept")->assertStatus(409);

    expect($connection->fresh()->status)->toBe('expired');
});

test('an agency initiating a connection on a property notifies its owner', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id, 'title' => 'Cozy Cottage']);
    $agency = Agency::factory()->create(['name' => 'Best Realtors']);
    $apiKey = AgencyApiKey::factory()->create(['agency_id' => $agency->id]);

    $response = $this->withHeader('X-Agency-Api-Key', $apiKey->api_key)
        ->postJson('/api/agency/connections', ['property_id' => $property->id]);

    $response->assertCreated();

    $notification = Notification::where('user_id', $owner->id)->first();
    expect($notification)->not->toBeNull();
    expect($notification->payload)->toBe(['type' => 'connection_request', 'connection_id' => $response->json('id')]);
});

test('an agency initiating a connection to a phone number does not create a notification', function () {
    $agency = Agency::factory()->create();
    $apiKey = AgencyApiKey::factory()->create(['agency_id' => $agency->id]);

    $this->withHeader('X-Agency-Api-Key', $apiKey->api_key)
        ->postJson('/api/agency/connections', ['target_phone' => '555-0100'])
        ->assertCreated();

    expect(Notification::count())->toBe(0);
});

test('index only returns connections on the authenticated user\'s properties', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id]);
    $otherProperty = Property::factory()->create(['user_id' => $other->id]);

    Connection::factory()->create(['property_id' => $ownProperty->id]);
    Connection::factory()->create(['property_id' => $otherProperty->id]);

    Sanctum::actingAs($owner);

    $response = $this->getJson('/api/connections');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});
