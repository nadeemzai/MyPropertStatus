<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">My Connections</h1>

        <button type="button" wire:click="$toggle('showForm')" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
            {{ $showForm ? 'Cancel' : 'Connect with an agency' }}
        </button>
    </div>

    @if ($error)
        <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">
            {{ $error }}
        </div>
    @endif

    @if ($showForm)
        <form wire:submit="sendRequest" class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
            @if ($formError)
                <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $formError }}</div>
            @endif

            <div>
                <x-input-label for="property_id" value="Property" />
                <select wire:model="property_id" id="property_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Select a property&hellip;</option>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="agency_id" value="Agency" />
                <select wire:model="agency_id" id="agency_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Select an agency&hellip;</option>
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('agency_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="message" value="Message (optional)" />
                <textarea wire:model="message" id="message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                <x-input-error :messages="$errors->get('message')" class="mt-2" />
            </div>

            <button type="submit" class="rounded-md bg-green-700 px-6 py-2 text-sm font-semibold text-white hover:bg-green-800">
                Send request
            </button>
        </form>
    @endif

    <div class="flex gap-6 border-b border-gray-200 text-sm font-medium">
        <button
            type="button"
            wire:click="setTab('received')"
            class="border-b-2 px-1 py-2 {{ $tab === 'received' ? 'border-green-700 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            From Agencies
        </button>
        <button
            type="button"
            wire:click="setTab('sent')"
            class="border-b-2 px-1 py-2 {{ $tab === 'sent' ? 'border-green-700 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            Sent by Me
        </button>
    </div>

    @if ($connections->isEmpty())
        <p class="text-gray-500">
            @if ($tab === 'sent')
                You haven't reached out to any agencies yet.
            @else
                No agency has reached out about your properties yet.
            @endif
        </p>
    @else
        <div class="space-y-4">
            @foreach ($connections as $connection)
                <div wire:key="connection-{{ $connection->id }}" class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-900">{{ $connection->property->title }}</div>
                            <div class="text-sm text-gray-500">{{ $connection->property->location }}</div>
                            <div class="mt-1 text-sm text-gray-700">
                                {{ $tab === 'sent' ? 'To' : 'From' }} {{ $connection->agency->name }}
                            </div>
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

                    @if ($tab === 'received' && $connection->status === 'pending')
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
