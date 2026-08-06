@props(['property'])

<article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
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
                <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                    {{ ucfirst($property->type) }}
                </span>
            @endif
        </div>

        @if ($property->location)
            <p class="text-sm text-gray-500">{{ $property->location }}</p>
        @endif

        <p class="text-lg font-semibold text-gray-900">
            @if ($property->price)
                {{ $property->currency ?? 'PKR' }} {{ number_format($property->price) }}
            @else
                Price on request
            @endif
        </p>

        @php($details = collect([
            isset($property->details['bedrooms']) ? $property->details['bedrooms'].' bed' : null,
            isset($property->details['bathrooms']) ? $property->details['bathrooms'].' bath' : null,
            isset($property->details['area_sqft']) ? $property->details['area_sqft'].' sqft' : null,
        ])->filter())
        @if ($details->isNotEmpty())
            <p class="text-sm text-gray-500">{{ $details->join(' · ') }}</p>
        @endif

        @php($agency = $property->listings->first()?->agency)
        @if ($agency)
            <p class="text-xs text-gray-400">Listed by {{ $agency->name }}</p>
        @endif
    </div>
</article>
