<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">My Listings</h1>

    @if ($error)
        <div class="rounded-md bg-danger-50 p-3 text-sm text-danger-700">
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
                                <x-status-badge :variant="match ($listing->status) {
                                    'available' => 'success',
                                    'pending' => 'warning',
                                    'rented' => 'danger',
                                    'sold' => 'info',
                                    default => 'neutral',
                                }">
                                    {{ ucfirst($listing->status) }}
                                </x-status-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (is_null($listing->user_approved))
                                    <button
                                        type="button"
                                        wire:click="approve({{ $listing->id }})"
                                        wire:confirm="Approve this agency's listing request?"
                                        class="text-brand-700 hover:text-brand-800"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="reject({{ $listing->id }})"
                                        wire:confirm="Reject this agency's listing request?"
                                        class="ml-3 text-danger-600 hover:text-danger-800"
                                    >
                                        Reject
                                    </button>
                                @elseif ($listing->user_approved && $listing->status !== 'archived')
                                    <button
                                        type="button"
                                        wire:click="removeAgency({{ $listing->id }})"
                                        wire:confirm="Remove this agency from the listing? This will archive the listing."
                                        class="text-danger-600 hover:text-danger-800"
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
