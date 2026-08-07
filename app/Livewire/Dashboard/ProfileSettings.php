<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ProfileSettings extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public $avatar = null;

    public ?string $statusMessage = null;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $passwordStatusMessage = null;

    public string $delete_password = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:255', Rule::unique('users', 'phone')->ignore($user->id)],
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $this->avatar->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);

        $this->avatar = null;
        $this->statusMessage = 'Profile updated.';
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        $validated = $this->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');

            return;
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->passwordStatusMessage = 'Password updated.';
    }

    public function deleteAccount(): void
    {
        $user = auth()->user();

        $this->validate(['delete_password' => 'required|string']);

        if (! Hash::check($this->delete_password, $user->password)) {
            $this->addError('delete_password', 'The password is incorrect.');

            return;
        }

        $user->delete();

        auth()->guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.profile-settings');
    }
}
