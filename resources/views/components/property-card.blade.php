@props(['property'])

<a href="{{ route('properties.show', $property->id) }}" class="block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:border-green-700 hover:shadow-md">
    <div class="aspect-video bg-gray-100">
        @php($image = $property->media->firstWhere('type', 'image'))
        @if ($image)
            <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $property->title }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-sm text-gray-400">
                No image
            </div>
        @endif
    </div>

    <div class="space-y-2 p-4">
        <div class="flex items-start justify-between gap-2">
            <h3 class="font-semibold text-gray-900">{{ $property->title }}</h3>

            @if ($property->type)
                <span class="shrink-0 rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                    {{ ucfirst($property->type) }}
                </span>
            @endif
        </div>

        @if ($property->location)
            <p class="text-sm text-gray-500">{{ $property->location }}</p>
        @endif

        <p class="text-lg font-semibold text-gray-900">
            <x-property-price :property="$property" />
        </p>

        <x-property-details-summary :property="$property" />

        @php($agency = $property->activeListing()?->agency)
        @if ($agency)
            <p class="text-xs text-gray-400">Listed by {{ $agency->name }}</p>
        @endif
    </div>
</a>
