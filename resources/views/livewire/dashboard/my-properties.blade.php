<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">My Properties</h1>
        <a href="{{ route('dashboard.properties.create') }}" class="rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            Add Property
        </a>
    </div>

    <div class="flex gap-6 border-b border-gray-200 text-sm font-medium">
        <button
            type="button"
            wire:click="setTab('active')"
            class="border-b-2 px-1 py-2 {{ $tab === 'active' ? 'border-brand-700 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            Active
        </button>
        <button
            type="button"
            wire:click="setTab('archived')"
            class="border-b-2 px-1 py-2 {{ $tab === 'archived' ? 'border-brand-700 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            Archived
        </button>
    </div>

    @if ($properties->isEmpty())
        <p class="text-gray-500">
            @if ($tab === 'archived')
                No archived properties.
            @else
                You haven't added any properties yet.
            @endif
        </p>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($properties as $property)
                        <tr wire:key="property-{{ $property->id }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $property->title }}</div>
                                <div class="text-gray-500">{{ $property->location }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :variant="match ($property->status) {
                                    'published' => 'success',
                                    'archived' => 'neutral',
                                    default => 'warning',
                                }">
                                    {{ ucfirst($property->status) }}
                                </x-status-badge>
                            </td>
                            <td class="px-4 py-3">
                                @if ($property->price)
                                    {{ $property->currency ?? 'PKR' }} {{ number_format($property->price) }}
                                @else
                                    <span class="text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.properties.edit', $property->id) }}" class="text-brand-700 hover:text-brand-800">Edit</a>
                                <button
                                    type="button"
                                    wire:click="delete({{ $property->id }})"
                                    wire:confirm="Delete this property? This cannot be undone."
                                    class="ml-3 text-danger-600 hover:text-danger-800"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $properties->links() }}
    @endif
</div>
