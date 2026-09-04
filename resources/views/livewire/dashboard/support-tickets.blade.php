<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Report a Problem</h1>

        <button type="button" wire:click="$toggle('showForm')" class="rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            {{ $showForm ? 'Cancel' : 'New ticket' }}
        </button>
    </div>

    @if ($showForm)
        <form wire:submit="createTicket" class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
            <div>
                <x-input-label for="subject" value="Subject" />
                <x-text-input wire:model="subject" id="subject" type="text" />
                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="message" value="Describe the problem" />
                <x-textarea-input wire:model="message" id="message" rows="4" class="mt-1" />
                <x-input-error :messages="$errors->get('message')" class="mt-2" />
            </div>

            <button type="submit" class="rounded-md bg-brand-700 px-6 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                Submit ticket
            </button>
        </form>
    @endif

    @if ($tickets->isEmpty())
        <p class="text-gray-500">You haven't reported any problems yet.</p>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tickets as $ticket)
                        <tr wire:key="ticket-{{ $ticket->id }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $ticket->subject }}</div>
                                <div class="text-gray-500">{{ \Illuminate\Support\Str::limit($ticket->message, 80) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :variant="match ($ticket->status) {
                                    'open' => 'warning',
                                    'in_progress' => 'info',
                                    'resolved' => 'success',
                                    'closed' => 'neutral',
                                    default => 'neutral',
                                }">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </x-status-badge>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $ticket->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $tickets->links() }}
    @endif
</div>
