<?php

namespace App\Livewire\Dashboard;

use App\Exceptions\ListingActionException;
use App\Services\ListingService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyListings extends Component
{
    use WithPagination;

    public ?string $error = null;

    public function approve(int $listingId): void
    {
        $this->respond($listingId, true);
    }

    public function reject(int $listingId): void
    {
        $this->respond($listingId, false);
    }

    private function respond(int $listingId, bool $approved): void
    {
        $listings = app(ListingService::class);
        $listing = $listings->mine(auth()->user())->findOrFail($listingId);

        try {
            $approved ? $listings->approve($listing, auth()->user()) : $listings->reject($listing, auth()->user());
            $this->error = null;
        } catch (ListingActionException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $listings = app(ListingService::class)->mine(auth()->user())
            ->with(['property', 'agency'])
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.my-listings', [
            'listings' => $listings,
        ]);
    }
}
