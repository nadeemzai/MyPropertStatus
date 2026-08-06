<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SkyProperty - SP') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <x-brand-logo />

                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-700">Dashboard</a>
                    <a href="{{ route('dashboard.properties.index') }}" class="text-gray-600 hover:text-green-700">My Properties</a>
                    <a href="{{ route('dashboard.listings.index') }}" class="text-gray-600 hover:text-green-700">My Listings</a>
                    <a href="{{ route('properties.index') }}" class="text-gray-600 hover:text-green-700">Browse properties</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-green-700">Log out</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-6 py-12">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
