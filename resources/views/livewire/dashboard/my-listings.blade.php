<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">My Listings</h1>

    @if ($error)
        <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">
            {{ $error }}
        </div>
    @endif

    @if ($listings->isEmpty())
        <p class="text-gray-500">No agency has proposed a listing on your properties yet.</p>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Agency</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($listings as $listing)
                        <tr wire:key="listing-{{ $listing->id }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $listing->property->title }}</div>
                                <div class="text-gray-500">{{ $listing->property->location }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $listing->agency->name }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ match ($listing->status) {
                                    'available' => 'bg-green-50 text-green-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'rented' => 'bg-red-50 text-red-700',
                                    'sold' => 'bg-blue-50 text-blue-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (is_null($listing->user_approved))
                                    <button
                                        type="button"
                                        wire:click="approve({{ $listing->id }})"
                                        wire:confirm="Approve this agency's listing request?"
                                        class="text-green-700 hover:text-green-800"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="reject({{ $listing->id }})"
                                        wire:confirm="Reject this agency's listing request?"
                                        class="ml-3 text-red-600 hover:text-red-800"
                                    >
                                        Reject
                                    </button>
                                @elseif ($listing->user_approved && $listing->status !== 'archived')
                                    <button
                                        type="button"
                                        wire:click="removeAgency({{ $listing->id }})"
                                        wire:confirm="Remove this agency from the listing? This will archive the listing."
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        Remove Agency
                                    </button>
                                @else
                                    <span class="text-gray-400">
                                        {{ $listing->user_approved ? 'Archived' : 'Rejected' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $listings->links() }}
    @endif
</div>
