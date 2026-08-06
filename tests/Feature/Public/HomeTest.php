<?php

use App\Livewire\Public\Home;
use App\Models\Property;
use Livewire\Livewire;

test('only published properties appear in recently listed', function () {
    Property::factory()->published()->create(['title' => 'Published One']);
    Property::factory()->draft()->create(['title' => 'Draft One']);

    Livewire::test(Home::class)
        ->assertSee('Published One')
        ->assertDontSee('Draft One');
});

test('shows an empty state when nothing is published', function () {
    Property::factory()->draft()->create();

    Livewire::test(Home::class)
        ->assertSee('No properties are published yet');
});
