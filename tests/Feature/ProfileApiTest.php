<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

test('a user can update their profile via the api', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson('/api/profile', ['name' => 'New Name'])
        ->assertOk()
        ->assertJsonPath('name', 'New Name');

    expect($user->fresh()->name)->toBe('New Name');
});

test('a user can upload an avatar via the api', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson('/api/profile', ['avatar' => UploadedFile::fake()->image('avatar.jpg')])
        ->assertOk();

    expect($user->fresh()->avatar)->not->toBeNull();
});

test('email must be unique when updating profile via the api', function () {
    $user = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);
    Sanctum::actingAs($user);

    $this->putJson('/api/profile', ['email' => 'taken@example.com'])
        ->assertStatus(422);
});

test('a user can delete their account via the api with the correct password', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);
    Sanctum::actingAs($user);

    $this->deleteJson('/api/profile', ['password' => 'correct-password'])
        ->assertOk();

    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id)->trashed())->toBeTrue();
});

test('deleting an account via the api fails with an incorrect password', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);
    Sanctum::actingAs($user);

    $this->deleteJson('/api/profile', ['password' => 'wrong-password'])
        ->assertStatus(422);

    expect($user->fresh())->not->toBeNull();
});
