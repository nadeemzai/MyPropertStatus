<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">My Properties</h1>
        <a href="{{ route('dashboard.properties.create') }}" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
            Add Property
        </a>
    </div>

    <div class="flex gap-6 border-b border-gray-200 text-sm font-medium">
        <button
            type="button"
            wire:click="setTab('active')"
            class="border-b-2 px-1 py-2 {{ $tab === 'active' ? 'border-green-700 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            Active
        </button>
        <button
            type="button"
            wire:click="setTab('archived')"
            class="border-b-2 px-1 py-2 {{ $tab === 'archived' ? 'border-green-700 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
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
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ match ($property->status) {
                                    'published' => 'bg-green-50 text-green-700',
                                    'archived' => 'bg-gray-100 text-gray-600',
                                    default => 'bg-amber-50 text-amber-700',
                                } }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($property->price)
                                    {{ $property->currency ?? 'PKR' }} {{ number_format($property->price) }}
                                @else
                                    <span class="text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.properties.edit', $property->id) }}" class="text-green-700 hover:text-green-800">Edit</a>
                                <button
                                    type="button"
                                    wire:click="delete({{ $property->id }})"
                                    wire:confirm="Delete this property? This cannot be undone."
                                    class="ml-3 text-red-600 hover:text-red-800"
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
