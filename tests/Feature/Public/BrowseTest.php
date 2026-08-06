<?php

use App\Livewire\Public\Browse;
use App\Models\Property;
use Livewire\Livewire;

test('only published properties are listed', function () {
    Property::factory()->published()->create(['title' => 'Published One']);
    Property::factory()->draft()->create(['title' => 'Draft One']);

    Livewire::test(Browse::class)
        ->assertSee('Published One')
        ->assertDontSee('Draft One');
});

test('location filter narrows results', function () {
    Property::factory()->published()->create(['title' => 'Lahore House', 'location' => 'Lahore']);
    Property::factory()->published()->create(['title' => 'Karachi House', 'location' => 'Karachi']);

    Livewire::test(Browse::class)
        ->set('location', 'Lahore')
        ->assertSee('Lahore House')
        ->assertDontSee('Karachi House');
});

test('type filter narrows results', function () {
    Property::factory()->published()->create(['title' => 'An Apartment', 'type' => 'apartment']);
    Property::factory()->published()->create(['title' => 'A House', 'type' => 'house']);

    Livewire::test(Browse::class)
        ->set('type', 'house')
        ->assertSee('A House')
        ->assertDontSee('An Apartment');
});

test('price range filter narrows results', function () {
    Property::factory()->published()->create(['title' => 'Cheap Place', 'price' => 1_000_000]);
    Property::factory()->published()->create(['title' => 'Expensive Place', 'price' => 90_000_000]);

    Livewire::test(Browse::class)
        ->set('max_price', '5000000')
        ->assertSee('Cheap Place')
        ->assertDontSee('Expensive Place');
});
