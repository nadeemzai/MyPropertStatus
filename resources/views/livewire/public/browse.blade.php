<div class="space-y-8">
    <h1 class="text-2xl font-semibold text-gray-900">Browse properties</h1>

    <div class="grid gap-4 rounded-lg border border-gray-200 bg-white p-4 sm:grid-cols-4">
        <div>
            <x-input-label for="location" value="Location" />
            <x-text-input wire:model.live.debounce.300ms="location" id="location" type="text" placeholder="City or area" />
        </div>

        <div>
            <x-input-label for="type" value="Type" />
            <select wire:model.live="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                <option value="">Any type</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="land">Land</option>
                <option value="commercial">Commercial</option>
            </select>
        </div>

        <div>
            <x-input-label for="min_price" value="Min price" />
            <x-text-input wire:model.live.debounce.300ms="min_price" id="min_price" type="number" min="0" />
        </div>

        <div>
            <x-input-label for="max_price" value="Max price" />
            <x-text-input wire:model.live.debounce.300ms="max_price" id="max_price" type="number" min="0" />
        </div>
    </div>

    @if ($properties->isEmpty())
        <p class="text-gray-500">No properties match your search.</p>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>

        {{ $properties->links() }}
    @endif
</div>
