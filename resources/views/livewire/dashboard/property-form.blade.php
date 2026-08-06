<div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">{{ $property ? 'Edit Property' : 'Add Property' }}</h1>

    <form wire:submit="save" class="space-y-4">
        <div>
            <x-input-label for="title" value="Title" />
            <x-text-input wire:model="title" id="title" type="text" />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" value="Description" />
            <textarea
                wire:model="description"
                id="description"
                rows="4"
                class="block w-full rounded-md border-gray-300 shadow-sm outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 sm:text-sm"
            ></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="type" value="Type" />
                <select wire:model="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 sm:text-sm">
                    <option value="">Select type</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="land">Land</option>
                    <option value="commercial">Commercial</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="location" value="Location" />
                <x-text-input wire:model="location" id="location" type="text" />
                <x-input-error :messages="$errors->get('location')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="currency" value="Currency" />
                <x-text-input wire:model="currency" id="currency" type="text" />
                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="price" value="Price" />
                <x-text-input wire:model="price" id="price" type="number" min="0" step="0.01" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="available_from" value="Available from" />
            <x-text-input wire:model="available_from" id="available_from" type="date" />
            <x-input-error :messages="$errors->get('available_from')" class="mt-2" />
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="bedrooms" value="Bedrooms" />
                <x-text-input wire:model="bedrooms" id="bedrooms" type="number" min="0" />
                <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="bathrooms" value="Bathrooms" />
                <x-text-input wire:model="bathrooms" id="bathrooms" type="number" min="0" />
                <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="area_sqft" value="Area (sqft)" />
                <x-text-input wire:model="area_sqft" id="area_sqft" type="number" min="0" />
                <x-input-error :messages="$errors->get('area_sqft')" class="mt-2" />
            </div>
        </div>

        @if ($property)
            <div>
                <x-input-label for="status" value="Status" />
                <select wire:model="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 sm:text-sm">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-md bg-green-700 px-6 py-2 text-sm font-semibold text-white hover:bg-green-800">
                {{ $property ? 'Save changes' : 'Add property' }}
            </button>
            <a href="{{ route('dashboard.properties.index') }}" class="text-sm text-gray-600 hover:text-green-700">Cancel</a>
        </div>
    </form>
</div>
