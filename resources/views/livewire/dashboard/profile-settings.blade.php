<div class="max-w-2xl space-y-10">
    <h1 class="text-2xl font-semibold text-gray-900">Profile & Settings</h1>

    <section class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900">Profile</h2>

        @if ($statusMessage)
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ $statusMessage }}</div>
        @endif

        <form wire:submit="updateProfile" class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-full bg-gray-100">
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar preview" class="h-full w-full object-cover">
                    @elseif (auth()->user()->avatar)
                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-xs text-gray-400">No photo</div>
                    @endif
                </div>

                <div>
                    <label class="inline-block cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Change photo
                        <input wire:model="avatar" type="file" accept="image/*" class="hidden">
                    </label>
                    <div wire:loading wire:target="avatar" class="mt-1 text-xs text-gray-500">Uploading&hellip;</div>
                    <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input wire:model="name" id="name" type="text" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input wire:model="email" id="email" type="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" value="Phone" />
                <x-text-input wire:model="phone" id="phone" type="text" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <button type="submit" class="rounded-md bg-green-700 px-6 py-2 text-sm font-semibold text-white hover:bg-green-800">
                Save changes
            </button>
        </form>
    </section>

    <section class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900">Change password</h2>

        @if ($passwordStatusMessage)
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ $passwordStatusMessage }}</div>
        @endif

        <form wire:submit="updatePassword" class="space-y-4">
            <div>
                <x-input-label for="current_password" value="Current password" />
                <x-text-input wire:model="current_password" id="current_password" type="password" autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="New password" />
                <x-text-input wire:model="password" id="password" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm new password" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="rounded-md bg-green-700 px-6 py-2 text-sm font-semibold text-white hover:bg-green-800">
                Update password
            </button>
        </form>
    </section>

    <section class="space-y-4 rounded-lg border border-red-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-red-700">Danger zone</h2>
        <p class="text-sm text-gray-600">
            Deleting your account is permanent. All of your properties, listings, and connections will remain associated
            with your account record but you will no longer be able to log in.
        </p>

        <form wire:submit="deleteAccount" wire:confirm="Are you absolutely sure you want to delete your account? This cannot be undone." class="space-y-4">
            <div class="max-w-sm">
                <x-input-label for="delete_password" value="Confirm your password" />
                <x-text-input wire:model="delete_password" id="delete_password" type="password" />
                <x-input-error :messages="$errors->get('delete_password')" class="mt-2" />
            </div>

            <button type="submit" class="rounded-md bg-red-600 px-6 py-2 text-sm font-semibold text-white hover:bg-red-700">
                Delete my account
            </button>
        </form>
    </section>
</div>
