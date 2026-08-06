<?php

use App\Livewire\Dashboard\PropertyForm;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

test('a user can create a property', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PropertyForm::class)
        ->set('title', 'Sunny Apartment')
        ->set('type', 'apartment')
        ->set('price', '5000000')
        ->set('location', 'Lahore')
        ->set('available_from', '2026-09-01')
        ->set('bedrooms', '2')
        ->call('save')
        ->assertRedirect(route('dashboard.properties.index'));

    $property = Property::where('title', 'Sunny Apartment')->first();

    expect($property)->not->toBeNull();
    expect($property->user_id)->toBe($user->id);
    expect($property->status)->toBe('draft');
    expect($property->available_from->format('Y-m-d'))->toBe('2026-09-01');
    expect($property->details['bedrooms'])->toBe(2);
});

test('status cannot be set on create', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PropertyForm::class)
        ->set('title', 'Some Place')
        ->set('status', 'published')
        ->call('save');

    $property = Property::where('title', 'Some Place')->first();

    expect($property->status)->toBe('draft');
});

test('title is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PropertyForm::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

test('editing loads the owner\'s existing property data', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $user->id,
        'title' => 'Existing Place',
        'status' => 'published',
    ]);

    Livewire::actingAs($user)
        ->test(PropertyForm::class, ['id' => $property->id])
        ->assertSet('title', 'Existing Place')
        ->assertSet('status', 'published');
});

test('a user cannot edit another user\'s property', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder);

    expect(fn () => Livewire::test(PropertyForm::class, ['id' => $property->id]))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('editing can change the status', function () {
    $user = User::factory()->create();
    $property = Property::factory()->draft()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(PropertyForm::class, ['id' => $property->id])
        ->set('status', 'published')
        ->call('save');

    expect($property->fresh()->status)->toBe('published');
});
