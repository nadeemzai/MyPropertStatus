<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MyPropertyStatus') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-900">
                    {{ config('app.name', 'MyPropertyStatus') }}
                </a>

                <nav class="flex items-center gap-4 text-sm">
                    @if (! request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                    @endif

                    @if (! request()->routeIs('register'))
                        <a href="{{ route('register') }}" class="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                            Register
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="flex flex-1 items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
