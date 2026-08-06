<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Register</h1>

    <form wire:submit="register" class="space-y-4">
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input wire:model="name" id="name" type="text" autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" type="email" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" value="Phone" />
            <x-text-input wire:model="phone" id="phone" type="text" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="password" id="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button>Register</x-primary-button>
    </form>
</div>
