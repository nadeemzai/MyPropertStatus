<?php

namespace App\Services;

use App\Exceptions\ListingActionException;
use App\Models\Agency;
use App\Models\Listing;

class ListingService
{
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
