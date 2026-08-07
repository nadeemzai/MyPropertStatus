<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">My Connections</h1>

    @if ($error)
        <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">
            {{ $error }}
        </div>
    @endif

    @if ($connections->isEmpty())
        <p class="text-gray-500">No agency has reached out about your properties yet.</p>
    @else
        <div class="space-y-4">
            @foreach ($connections as $connection)
                <div wire:key="connection-{{ $connection->id }}" class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-900">{{ $connection->property->title }}</div>
                            <div class="text-sm text-gray-500">{{ $connection->property->location }}</div>
                            <div class="mt-1 text-sm text-gray-700">From {{ $connection->agency->name }}</div>
                        </div>

                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ match ($connection->status) {
                            'accepted' => 'bg-green-50 text-green-700',
                            'pending' => 'bg-amber-50 text-amber-700',
                            'rejected' => 'bg-red-50 text-red-700',
                            default => 'bg-gray-100 text-gray-600',
                        } }}">
                            {{ ucfirst($connection->status) }}
                        </span>
                    </div>

                    @if ($connection->message)
                        <p class="mt-3 text-sm text-gray-600">&ldquo;{{ $connection->message }}&rdquo;</p>
                    @endif

                    @if ($connection->status === 'pending')
                        <div class="mt-4">
                            <button
                                type="button"
                                wire:click="accept({{ $connection->id }})"
                                wire:confirm="Accept this agency's connection request?"
                                class="text-green-700 hover:text-green-800"
                            >
                                Accept
                            </button>
                            <button
                                type="button"
                                wire:click="reject({{ $connection->id }})"
                                wire:confirm="Reject this agency's connection request?"
                                class="ml-3 text-red-600 hover:text-red-800"
                            >
                                Reject
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{ $connections->links() }}
    @endif
</div>
