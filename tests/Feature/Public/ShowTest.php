<?php

use App\Livewire\Public\Show;
use App\Models\Property;
use App\Models\PropertyMedia;
use Livewire\Livewire;

test('published property detail page renders its content', function () {
    $property = Property::factory()->published()->create([
        'title' => 'Lovely Cottage',
        'description' => 'A lovely little cottage by the lake.',
    ]);

    Livewire::test(Show::class, ['id' => $property->id])
        ->assertSee('Lovely Cottage')
        ->assertSee('A lovely little cottage by the lake.');
});

test('draft property detail page 404s', function () {
    $draft = Property::factory()->draft()->create();

    $this->get("/properties/{$draft->id}")->assertNotFound();
});

test('published property detail page is reachable over http', function () {
    $property = Property::factory()->published()->create();

    $this->get("/properties/{$property->id}")->assertOk();
});

test('gallery indices stay sequential when a non-image media item sorts first', function () {
    $property = Property::factory()->published()->create();

    // A non-image item with a lower sort position would leave gaps in the
    // media collection's keys if the image list isn't re-indexed, breaking
    // the gallery's active-index toggle (x-show="active === N").
    PropertyMedia::factory()->create(['property_id' => $property->id, 'type' => 'video', 'path' => 'videos/tour.mp4', 'sort_order' => 0]);
    PropertyMedia::factory()->create(['property_id' => $property->id, 'type' => 'image', 'path' => 'images/one.jpg', 'sort_order' => 1]);
    PropertyMedia::factory()->create(['property_id' => $property->id, 'type' => 'image', 'path' => 'images/two.jpg', 'sort_order' => 2]);

    Livewire::test(Show::class, ['id' => $property->id])
        ->assertSeeHtml('active === 0')
        ->assertSeeHtml('active === 1')
        ->assertDontSeeHtml('active === 2');
});
