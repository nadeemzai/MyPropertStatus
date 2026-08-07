<?php

namespace App\Services;

use App\Exceptions\ListingActionException;
use App\Models\Agency;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ListingService
{
    /**
     * Base query for listings on properties owned by the given user.
     */
    public function mine(User $user): Builder
    {
        return Listing::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Property owner approves a proposed listing.
     */
    public function approve(Listing $listing, User $user): Listing
    {
        return $this->respond($listing, $user, true, 'available');
    }

    /**
     * Property owner rejects a proposed listing.
     */
    public function reject(Listing $listing, User $user): Listing
    {
        return $this->respond($listing, $user, false, 'archived');
    }

    private function respond(Listing $listing, User $user, bool $approved, string $newStatus): Listing
    {
        if ($listing->property->user_id !== $user->id) {
            throw new ListingActionException('You do not own the property this listing is for.', 403);
        }

        if (! is_null($listing->user_approved)) {
            throw new ListingActionException('This listing has already been responded to.', 409);
        }

        $fromStatus = $listing->status;

        $listing->update([
            'user_approved' => $approved,
            'approved_at' => now(),
            'status' => $newStatus,
        ]);

        $listing->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $newStatus,
            'changed_by_user_id' => $user->id,
        ]);

        return $listing;
    }

    /**
     * Property owner removes the agency from a listing that was previously approved,
     * ending the arrangement without deleting the listing's history.
     */
    public function removeAgency(Listing $listing, User $user): Listing
    {
        if ($listing->property->user_id !== $user->id) {
            throw new ListingActionException('You do not own the property this listing is for.', 403);
        }

        if (! $listing->user_approved) {
            throw new ListingActionException('This listing has not been approved, so there is no agency to remove.', 409);
        }

        if ($listing->status === 'archived') {
            throw new ListingActionException('This listing has already been archived.', 409);
        }

        $fromStatus = $listing->status;

        $listing->update(['status' => 'archived']);

        $listing->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => 'archived',
            'changed_by_user_id' => $user->id,
            'reason' => 'Agency removed by property owner.',
        ]);

        return $listing;
    }

    /**
     * Agency proposes a listing for a property.
     */
    public function propose(Agency $agency, int $propertyId, ?string $agencyNotes = null): Listing
    {
        $exists = Listing::where('property_id', $propertyId)
            ->where('agency_id', $agency->id)
            ->exists();

        if ($exists) {
            throw new ListingActionException(
                'A listing for this property already exists for your agency.',
                409
            );
        }

        return Listing::create([
            'property_id' => $propertyId,
            'agency_id' => $agency->id,
            'status' => 'pending',
            'agency_proposed' => true,
            'agency_notes' => $agencyNotes,
        ]);
    }

    /**
     * Agency updates the status of a listing the property owner has approved.
     */
    public function updateStatus(Listing $listing, Agency $agency, string $newStatus, ?string $reason = null): Listing
    {
        if ($listing->agency_id !== $agency->id) {
            throw new ListingActionException('You do not own this listing.', 403);
        }

        if (! $listing->user_approved) {
            throw new ListingActionException(
                'This listing has not been approved by the property owner yet.',
                403
            );
        }

        $fromStatus = $listing->status;

        $listing->update(['status' => $newStatus]);

        $listing->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $newStatus,
            'changed_by_agency_id' => $agency->id,
            'reason' => $reason,
        ]);

        return $listing;
    }
}
