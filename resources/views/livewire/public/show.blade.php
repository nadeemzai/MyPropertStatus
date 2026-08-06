<div class="space-y-8">
    <a href="{{ route('properties.index') }}" class="text-sm text-gray-600 hover:text-green-700">&larr; Back to browse</a>

    @php($images = $property->media->where('type', 'image')->values())

    <div x-data="{ active: 0 }" class="space-y-3">
        <div class="aspect-video overflow-hidden rounded-lg bg-gray-100">
            @if ($images->isNotEmpty())
                @foreach ($images as $index => $image)
                    <img
                        x-show="active === {{ $index }}"
                        src="{{ asset('storage/'.$image->path) }}"
                        alt="{{ $property->title }}"
                        class="h-full w-full object-cover"
                    >
                @endforeach
            @else
                <div class="flex h-full w-full items-center justify-center text-sm text-gray-400">
                    No image
                </div>
            @endif
        </div>

        @if ($images->count() > 1)
            <div class="flex gap-2 overflow-x-auto">
                @foreach ($images as $index => $image)
                    <button
                        type="button"
                        @click="active = {{ $index }}"
                        :class="active === {{ $index }} ? 'ring-2 ring-green-700' : 'ring-1 ring-gray-200'"
                        class="h-16 w-24 shrink-0 overflow-hidden rounded-md"
                    >
                        <img src="{{ asset('storage/'.$image->path) }}" alt="" class="h-full w-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="flex items-start justify-between gap-2">
                <h1 class="text-2xl font-semibold text-gray-900">{{ $property->title }}</h1>

                @if ($property->type)
                    <span class="shrink-0 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                        {{ ucfirst($property->type) }}
                    </span>
                @endif
            </div>

            @if ($property->location)
                <p class="text-gray-500">{{ $property->location }}</p>
            @endif

            <x-property-details-summary :property="$property" class="text-base text-gray-700" />

            @if ($property->description)
                <p class="whitespace-pre-line pt-4 text-gray-700">{{ $property->description }}</p>
            @endif
        </div>

        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
            <p class="text-2xl font-semibold text-gray-900">
                <x-property-price :property="$property" />
            </p>

            @php($agency = $property->activeListing()?->agency)
            @if ($agency)
                <div class="space-y-1 border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-900">Listed by</p>
                    <p class="text-sm text-gray-700">{{ $agency->name }}</p>

                    @if ($agency->phone)
                        <p class="text-sm text-gray-500">{{ $agency->phone }}</p>
                    @endif

                    @if ($agency->email)
                        <p class="text-sm text-gray-500">{{ $agency->email }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
