<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Log in</h1>

    @if (session('status'))
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-4">
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" type="email" autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="password" id="password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                Remember me
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Forgot password?
            </a>
        </div>

        <x-primary-button>Log in</x-primary-button>
    </form>
</div>
