<?php

use App\Models\Agency;
use App\Models\Listing;
use App\Models\Property;

test('active listing ignores unapproved listings', function () {
    $property = Property::factory()->create();

    Listing::factory()->create([
        'property_id' => $property->id,
        'user_approved' => null,
        'status' => 'pending',
    ]);

    expect($property->activeListing())->toBeNull();
});

test('active listing ignores archived listings even if approved', function () {
    $property = Property::factory()->create();

    Listing::factory()->create([
        'property_id' => $property->id,
        'user_approved' => true,
        'status' => 'archived',
        'approved_at' => now(),
    ]);

    expect($property->activeListing())->toBeNull();
});

test('active listing picks the most recently approved listing', function () {
    $property = Property::factory()->create();
    $older = Agency::factory()->create();
    $newer = Agency::factory()->create();

    Listing::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $older->id,
        'user_approved' => true,
        'status' => 'available',
        'approved_at' => now()->subDay(),
    ]);

    Listing::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $newer->id,
        'user_approved' => true,
        'status' => 'available',
        'approved_at' => now(),
    ]);

    expect($property->activeListing()->agency_id)->toBe($newer->id);
});
