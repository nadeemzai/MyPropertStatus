<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Property;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /**
     * List listings on properties owned by the authenticated user.
     */
    public function index(Request $request)
    {
        $query = Listing::whereHas('property', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->with(['property', 'agency'])->paginate(10);

        return response()->json($listings);
    }

    /**
     * Agency proposes a listing for a property.
     */
    public function store(Request $request)
    {
        $agency = $request->user();

        $validated = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'agency_notes' => 'nullable|string',
        ]);

        $exists = Listing::where('property_id', $validated['property_id'])
            ->where('agency_id', $agency->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A listing for this property already exists for your agency.',
            ], 409);
        }

        $listing = Listing::create([
            'property_id' => $validated['property_id'],
            'agency_id' => $agency->id,
            'status' => 'pending',
            'agency_proposed' => true,
            'agency_notes' => $validated['agency_notes'] ?? null,
        ]);

        return response()->json($listing, 201);
    }

    /**
     * Agency updates the status of a listing the property owner has approved.
     */
    public function updateStatus(Request $request, $id)
    {
        $agency = $request->user();
        $listing = Listing::findOrFail($id);

        if ($listing->agency_id !== $agency->id) {
            abort(403, 'You do not own this listing.');
        }

        if (! $listing->user_approved) {
            return response()->json([
                'message' => 'This listing has not been approved by the property owner yet.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:available,rented,sold,archived',
            'reason' => 'nullable|string',
        ]);

        $fromStatus = $listing->status;

        $listing->update(['status' => $validated['status']]);

        $listing->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $validated['status'],
            'changed_by_agency_id' => $agency->id,
            'reason' => $validated['reason'] ?? null,
        ]);

        return response()->json($listing);
    }

    /**
     * Property owner approves a proposed listing.
     */
    public function approve(Request $request, $id)
    {
        return $this->respond($request, $id, true, 'available');
    }

    /**
     * Property owner rejects a proposed listing.
     */
    public function reject(Request $request, $id)
    {
        return $this->respond($request, $id, false, 'archived');
    }

    private function respond(Request $request, $id, bool $approved, string $newStatus)
    {
        $listing = Listing::with('property')->findOrFail($id);

        if ($listing->property->user_id !== $request->user()->id) {
            abort(403, 'You do not own the property this listing is for.');
        }

        if (! is_null($listing->user_approved)) {
            return response()->json(['message' => 'This listing has already been responded to.'], 409);
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
            'changed_by_user_id' => $request->user()->id,
        ]);

        return response()->json($listing);
    }
}
