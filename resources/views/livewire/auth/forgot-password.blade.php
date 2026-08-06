<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Forgot password</h1>

    <p class="text-sm text-gray-600">
        Enter your email and we'll send you a password reset link.
    </p>

    @if ($status)
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">
            {{ $status }}
        </div>
    @endif

    <form wire:submit="sendResetLink" class="space-y-4">
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" type="email" autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button>Send password reset link</x-primary-button>
    </form>
</div>
