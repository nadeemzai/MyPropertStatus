<?php

use App\Models\Listing;
use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('owner can approve a listing via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/listings/{$listing->id}/approve")
        ->assertOk()
        ->assertJsonPath('status', 'available')
        ->assertJsonPath('user_approved', true);
});

test('owner can reject a listing via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/listings/{$listing->id}/reject")
        ->assertOk()
        ->assertJsonPath('status', 'archived')
        ->assertJsonPath('user_approved', false);
});

test('a non-owner cannot approve a listing via the api', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($intruder);

    $this->postJson("/api/listings/{$listing->id}/approve")->assertForbidden();
});

test('approving an already-responded listing conflicts', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/listings/{$listing->id}/approve")->assertOk();
    $this->postJson("/api/listings/{$listing->id}/reject")->assertStatus(409);
});

test('owner can remove the agency from an approved listing via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/listings/{$listing->id}/remove-agency")
        ->assertOk()
        ->assertJsonPath('status', 'archived');
});

test('a non-owner cannot remove the agency from a listing via the api', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);

    Sanctum::actingAs($intruder);

    $this->postJson("/api/listings/{$listing->id}/remove-agency")->assertForbidden();
});

test('removing the agency from a listing that was never approved conflicts via the api', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Sanctum::actingAs($owner);

    $this->postJson("/api/listings/{$listing->id}/remove-agency")->assertStatus(409);
});

test('index only returns listings on the authenticated user\'s properties', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id]);
    $otherProperty = Property::factory()->create(['user_id' => $other->id]);

    Listing::factory()->create(['property_id' => $ownProperty->id]);
    Listing::factory()->create(['property_id' => $otherProperty->id]);

    Sanctum::actingAs($owner);

    $response = $this->getJson('/api/listings');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});
