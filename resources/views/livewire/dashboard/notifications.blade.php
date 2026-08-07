<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>

    @if ($error)
        <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">
            {{ $error }}
        </div>
    @endif

    @if ($notifications->isEmpty())
        <p class="text-gray-500">You don't have any notifications yet.</p>
    @else
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <div
                    wire:key="notification-{{ $notification->id }}"
                    class="rounded-lg border bg-white p-4 {{ $notification->is_read ? 'border-gray-200' : 'border-green-200 bg-green-50/40' }}"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                @unless ($notification->is_read)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-green-600"></span>
                                @endunless
                                <div class="font-medium text-gray-900">{{ $notification->title }}</div>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                        </div>

                        <span class="shrink-0 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        @if ($notification->is_pending)
                            <button
                                type="button"
                                wire:click="accept({{ $notification->id }})"
                                wire:confirm="Accept this request?"
                                class="text-green-700 hover:text-green-800"
                            >
                                Accept
                            </button>
                            <button
                                type="button"
                                wire:click="decline({{ $notification->id }})"
                                wire:confirm="Decline this request?"
                                class="text-red-600 hover:text-red-800"
                            >
                                Decline
                            </button>
                        @elseif (! $notification->is_read)
                            <button
                                type="button"
                                wire:click="markRead({{ $notification->id }})"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Mark as read
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{ $notifications->links() }}
    @endif
</div>
