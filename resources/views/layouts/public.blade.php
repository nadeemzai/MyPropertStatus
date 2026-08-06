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
                    <a href="{{ route('properties.index') }}" class="text-gray-600 hover:text-green-700">Browse properties</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-700">Dashboard</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-green-700">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-700">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-green-700 px-4 py-2 text-white hover:bg-green-800">
                            Register
                        </a>
                    @endauth
                </nav>
            </div>

            <div class="border-t border-gray-100">
                <div class="mx-auto flex max-w-6xl items-center gap-6 px-6 py-2.5 text-sm">
                    @php($currentType = request()->query('type'))

                    <a href="{{ route('properties.index') }}" class="font-semibold {{ request()->routeIs('properties.index') && ! $currentType ? 'text-green-700' : 'text-gray-500 hover:text-green-700' }}">
                        All
                    </a>
                    <a href="{{ route('properties.index', ['type' => 'apartment']) }}" class="{{ $currentType === 'apartment' ? 'font-semibold text-green-700' : 'text-gray-500 hover:text-green-700' }}">
                        Apartments
                    </a>
                    <a href="{{ route('properties.index', ['type' => 'house']) }}" class="{{ $currentType === 'house' ? 'font-semibold text-green-700' : 'text-gray-500 hover:text-green-700' }}">
                        Houses
                    </a>
                    <a href="{{ route('properties.index', ['type' => 'land']) }}" class="{{ $currentType === 'land' ? 'font-semibold text-green-700' : 'text-gray-500 hover:text-green-700' }}">
                        Land
                    </a>
                    <a href="{{ route('properties.index', ['type' => 'commercial']) }}" class="{{ $currentType === 'commercial' ? 'font-semibold text-green-700' : 'text-gray-500 hover:text-green-700' }}">
                        Commercial
                    </a>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-6 py-12">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
