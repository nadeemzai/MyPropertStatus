<?php

use App\Exceptions\ListingActionException;
use App\Models\Listing;
use App\Models\Property;
use App\Models\User;
use App\Services\ListingService;

test('owner can approve a pending listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    $result = app(ListingService::class)->approve($listing, $owner);

    expect($result->user_approved)->toBeTrue();
    expect($result->status)->toBe('available');
    expect($result->approved_at)->not->toBeNull();
    expect($listing->statusHistories()->count())->toBe(1);
});

test('owner can reject a pending listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    $result = app(ListingService::class)->reject($listing, $owner);

    expect($result->user_approved)->toBeFalse();
    expect($result->status)->toBe('archived');
});

test('a non-owner cannot approve a listing', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    expect(fn () => app(ListingService::class)->approve($listing, $intruder))
        ->toThrow(ListingActionException::class);
});

test('a listing that has already been responded to cannot be responded to again', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    app(ListingService::class)->approve($listing, $owner);

    expect(fn () => app(ListingService::class)->reject($listing->fresh(), $owner))
        ->toThrow(ListingActionException::class);
});

test('mine() only returns listings on the given user\'s properties', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id]);
    $otherProperty = Property::factory()->create(['user_id' => $other->id]);

    Listing::factory()->create(['property_id' => $ownProperty->id]);
    Listing::factory()->create(['property_id' => $otherProperty->id]);

    expect(app(ListingService::class)->mine($owner)->count())->toBe(1);
});
