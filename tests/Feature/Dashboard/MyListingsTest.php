<?php

use App\Livewire\Dashboard\MyListings;
use App\Models\Agency;
use App\Models\Listing;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.listings.index'))->assertRedirect(route('login'));
});

test('only listings on the authenticated user\'s properties are shown', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id, 'title' => 'My House']);
    $otherProperty = Property::factory()->create(['user_id' => $other->id, 'title' => 'Their House']);

    Listing::factory()->create(['property_id' => $ownProperty->id]);
    Listing::factory()->create(['property_id' => $otherProperty->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->assertSee('My House')
        ->assertDontSee('Their House');
});

test('a pending listing shows approve and reject buttons', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    Listing::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->assertSee('Approve')
        ->assertSee('Reject');
});

test('owner can approve a listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->call('approve', $listing->id);

    expect($listing->fresh()->status)->toBe('available');
    expect($listing->fresh()->user_approved)->toBeTrue();
});

test('owner can reject a listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->call('reject', $listing->id);

    expect($listing->fresh()->status)->toBe('archived');
    expect($listing->fresh()->user_approved)->toBeFalse();
});

test('a user cannot respond to a listing on another user\'s property', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    $this->actingAs($intruder);

    expect(fn () => Livewire::test(MyListings::class)->call('approve', $listing->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect($listing->fresh()->user_approved)->toBeNull();
});

test('responding to an already-responded listing shows an error', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $listing = Listing::factory()->create(['property_id' => $property->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->call('approve', $listing->id)
        ->call('reject', $listing->id)
        ->assertSet('error', 'This listing has already been responded to.');
});

test('agency name is shown for each listing', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create(['name' => 'Best Realtors']);
    Listing::factory()->create(['property_id' => $property->id, 'agency_id' => $agency->id]);

    Livewire::actingAs($owner)
        ->test(MyListings::class)
        ->assertSee('Best Realtors');
});
