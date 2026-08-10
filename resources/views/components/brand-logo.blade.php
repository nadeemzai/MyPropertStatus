@php
    $appName = config('app.name', 'SkyProperty - SP');
    [$brandName, $brandTag] = str_contains($appName, ' - ')
        ? explode(' - ', $appName, 2)
        : [$appName, null];
@endphp

<a href="{{ route('home') }}" class="flex items-baseline gap-1.5">
    <span class="text-lg font-bold text-brand-700">{{ $brandName }}</span>

    @if ($brandTag)
        <span class="rounded bg-brand-700 px-1 py-0.5 text-[10px] font-bold leading-none text-white">{{ $brandTag }}</span>
    @endif
</a>
