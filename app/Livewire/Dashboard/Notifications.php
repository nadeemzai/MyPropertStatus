<?php

namespace App\Livewire\Dashboard;

use App\Exceptions\ConnectionActionException;
use App\Exceptions\ListingActionException;
use App\Models\Connection;
use App\Models\Listing;
use App\Models\Notification;
use App\Services\ConnectionService;
use App\Services\ListingService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Notifications extends Component
{
    use WithPagination;

    public ?string $error = null;

    public function markRead(int $notificationId): void
    {
        auth()->user()->notifications()->findOrFail($notificationId)->update(['is_read' => true]);
    }

    public function accept(int $notificationId): void
    {
        $this->act($notificationId, true);
    }

    public function decline(int $notificationId): void
    {
        $this->act($notificationId, false);
    }

    private function act(int $notificationId, bool $accepted): void
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $payload = $notification->payload ?? [];

        try {
            match ($payload['type'] ?? null) {
                'listing_proposed' => $this->respondToListing($payload['listing_id'], $accepted),
                'connection_request' => $this->respondToConnection($payload['connection_id'], $accepted),
                default => throw new ListingActionException('This notification cannot be acted on.', 422),
            };

            $notification->update(['is_read' => true]);
            $this->error = null;
        } catch (ListingActionException|ConnectionActionException $e) {
            $this->error = $e->getMessage();
        }
    }

    private function respondToListing(int $listingId, bool $accepted): void
    {
        $listings = app(ListingService::class);
        $listing = $listings->mine(auth()->user())->findOrFail($listingId);

        $accepted ? $listings->approve($listing, auth()->user()) : $listings->reject($listing, auth()->user());
    }

    private function respondToConnection(int $connectionId, bool $accepted): void
    {
        $connections = app(ConnectionService::class);
        $connection = $connections->mine(auth()->user())->findOrFail($connectionId);

        $accepted ? $connections->accept($connection, auth()->user()) : $connections->reject($connection, auth()->user());
    }

    private function isPending(Notification $notification): bool
    {
        $payload = $notification->payload ?? [];

        return match ($payload['type'] ?? null) {
            'listing_proposed' => is_null(optional(Listing::find($payload['listing_id'] ?? null))->user_approved),
            'connection_request' => optional(Connection::find($payload['connection_id'] ?? null))->status === 'pending',
            default => false,
        };
    }

    public function render()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);

        $notifications->each(fn (Notification $notification) => $notification->is_pending = $this->isPending($notification));

        return view('livewire.dashboard.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
