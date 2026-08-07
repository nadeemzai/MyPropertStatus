<?php

use App\Livewire\Dashboard\ProfileSettings;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('dashboard.settings'))->assertRedirect(route('login'));
});

test('mount fills in the current user\'s details', function () {
    $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '5551234']);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->assertSet('name', 'Jane Doe')
        ->assertSet('email', 'jane@example.com')
        ->assertSet('phone', '5551234');
});

test('a user can update their name, email, and phone', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->set('phone', '9998887777')
        ->call('updateProfile')
        ->assertSet('statusMessage', 'Profile updated.');

    expect($user->fresh())
        ->name->toBe('Updated Name')
        ->email->toBe('updated@example.com')
        ->phone->toBe('9998887777');
});

test('email must be unique among other users', function () {
    $user = User::factory()->create();
    $other = User::factory()->create(['email' => 'taken@example.com']);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('email', 'taken@example.com')
        ->call('updateProfile')
        ->assertHasErrors(['email']);
});

test('a user can upload a new avatar and the old one is deleted', function () {
    Storage::fake('public');
    $user = User::factory()->create(['avatar' => 'avatars/old.jpg']);
    Storage::disk('public')->put('avatars/old.jpg', 'fake-content');

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('avatar', UploadedFile::fake()->image('new.jpg'))
        ->call('updateProfile');

    Storage::disk('public')->assertMissing('avatars/old.jpg');
    expect($user->fresh()->avatar)->not->toBeNull();
});

test('a user can update their password with the correct current password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('current_password', 'old-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertSet('passwordStatusMessage', 'Password updated.');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('updating the password fails with an incorrect current password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('a user can delete their account with the correct password', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('delete_password', 'correct-password')
        ->call('deleteAccount')
        ->assertRedirect(route('home'));

    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id)->trashed())->toBeTrue();
});

test('deleting the account fails with an incorrect password and the account is kept', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    Livewire::actingAs($user)
        ->test(ProfileSettings::class)
        ->set('delete_password', 'wrong-password')
        ->call('deleteAccount')
        ->assertHasErrors(['delete_password']);

    expect($user->fresh())->not->toBeNull();
});
