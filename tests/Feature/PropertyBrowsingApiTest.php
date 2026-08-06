<?php

use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('public index only returns published properties', function () {
    Property::factory()->published()->create(['title' => 'Published One']);
    Property::factory()->draft()->create(['title' => 'Draft One']);

    $response = $this->getJson('/api/properties');

    $response->assertOk();
    $titles = collect($response->json('data'))->pluck('title');

    expect($titles)->toContain('Published One');
    expect($titles)->not->toContain('Draft One');
});

test('public show 404s for a draft property', function () {
    $draft = Property::factory()->draft()->create();

    $this->getJson("/api/properties/{$draft->id}")->assertNotFound();
});

test('public show returns a published property', function () {
    $published = Property::factory()->published()->create();

    $this->getJson("/api/properties/{$published->id}")
        ->assertOk()
        ->assertJsonPath('id', $published->id);
});

test('my-properties returns the owner\'s properties regardless of status', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    Property::factory()->published()->create(['user_id' => $owner->id]);
    Property::factory()->draft()->create(['user_id' => $owner->id]);
    Property::factory()->published()->create(['user_id' => $other->id]);

    Sanctum::actingAs($owner);

    $response = $this->getJson('/api/my-properties');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

test('my-properties requires authentication', function () {
    $this->getJson('/api/my-properties')->assertUnauthorized();
});
