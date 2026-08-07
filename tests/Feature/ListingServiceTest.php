<?php

use App\Exceptions\ListingActionException;
use App\Models\Agency;
use App\Models\Listing;
use App\Models\Notification;
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

test('proposing a listing notifies the property owner', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id, 'title' => 'Cozy Cottage']);
    $agency = Agency::factory()->create(['name' => 'Best Realtors']);

    $listing = app(ListingService::class)->propose($agency, $property->id);

    $notification = Notification::where('user_id', $owner->id)->first();

    expect($notification)->not->toBeNull();
    expect($notification->payload)->toBe(['type' => 'listing_proposed', 'listing_id' => $listing->id]);
    expect($notification->message)->toContain('Best Realtors')->toContain('Cozy Cottage');
});

test('owner can remove the agency from an approved listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);

    $result = app(ListingService::class)->removeAgency($listing, $owner);

    expect($result->status)->toBe('archived');
    expect($listing->statusHistories()->count())->toBe(1);
});

test('a non-owner cannot remove the agency from a listing', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);

    expect(fn () => app(ListingService::class)->removeAgency($listing, $intruder))
        ->toThrow(ListingActionException::class);
});

test('the agency cannot be removed from a listing that was never approved', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    expect(fn () => app(ListingService::class)->removeAgency($listing, $owner))
        ->toThrow(ListingActionException::class);

    expect($listing->fresh()->status)->toBe('pending');
});

test('the agency cannot be removed twice from an already-archived listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create([
        'property_id' => $property->id,
        'status' => 'available',
        'user_approved' => true,
    ]);

    app(ListingService::class)->removeAgency($listing, $owner);

    expect(fn () => app(ListingService::class)->removeAgency($listing->fresh(), $owner))
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
