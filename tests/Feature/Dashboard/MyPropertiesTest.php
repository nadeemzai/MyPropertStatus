<?php

use App\Livewire\Dashboard\MyProperties;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.properties.index'))->assertRedirect(route('login'));
});

test('only the authenticated user\'s own properties are listed', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    Property::factory()->published()->create(['user_id' => $owner->id, 'title' => 'My Place']);
    Property::factory()->published()->create(['user_id' => $other->id, 'title' => 'Their Place']);

    Livewire::actingAs($owner)
        ->test(MyProperties::class)
        ->assertSee('My Place')
        ->assertDontSee('Their Place');
});

test('active tab excludes archived properties', function () {
    $user = User::factory()->create();

    Property::factory()->draft()->create(['user_id' => $user->id, 'title' => 'Draft Place']);
    Property::factory()->create(['user_id' => $user->id, 'status' => 'archived', 'title' => 'Archived Place']);

    Livewire::actingAs($user)
        ->test(MyProperties::class)
        ->assertSee('Draft Place')
        ->assertDontSee('Archived Place');
});

test('archived tab only shows archived properties', function () {
    $user = User::factory()->create();

    Property::factory()->draft()->create(['user_id' => $user->id, 'title' => 'Draft Place']);
    Property::factory()->create(['user_id' => $user->id, 'status' => 'archived', 'title' => 'Archived Place']);

    Livewire::actingAs($user)
        ->test(MyProperties::class)
        ->call('setTab', 'archived')
        ->assertSee('Archived Place')
        ->assertDontSee('Draft Place');
});

test('a user can delete their own property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(MyProperties::class)
        ->call('delete', $property->id);

    expect(Property::find($property->id))->toBeNull();
});

test('a user cannot delete another user\'s property', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder);

    expect(fn () => Livewire::test(MyProperties::class)->call('delete', $property->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect(Property::find($property->id))->not->toBeNull();
});
