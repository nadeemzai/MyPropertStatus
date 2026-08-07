<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ListingActionException;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct(private ListingService $listings) {}

    /**
     * List listings on properties owned by the authenticated user.
     */
    public function index(Request $request)
    {
        $query = $this->listings->mine($request->user());

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
        $validated = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'agency_notes' => 'nullable|string',
        ]);

        try {
            $listing = $this->listings->propose(
                $request->user(),
                $validated['property_id'],
                $validated['agency_notes'] ?? null
            );
        } catch (ListingActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($listing, 201);
    }

    /**
     * Agency updates the status of a listing the property owner has approved.
     */
    public function updateStatus(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:available,rented,sold,archived',
            'reason' => 'nullable|string',
        ]);

        try {
            $listing = $this->listings->updateStatus(
                $listing,
                $request->user(),
                $validated['status'],
                $validated['reason'] ?? null
            );
        } catch (ListingActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($listing);
    }

    /**
     * Property owner approves a proposed listing.
     */
    public function approve(Request $request, $id)
    {
        return $this->respond($request, $id, true);
    }

    /**
     * Property owner rejects a proposed listing.
     */
    public function reject(Request $request, $id)
    {
        return $this->respond($request, $id, false);
    }

    /**
     * Property owner removes the agency from a previously approved listing.
     */
    public function removeAgency(Request $request, $id)
    {
        $listing = Listing::with('property')->findOrFail($id);

        try {
            $listing = $this->listings->removeAgency($listing, $request->user());
        } catch (ListingActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($listing);
    }

    private function respond(Request $request, $id, bool $approved)
    {
        $listing = Listing::with('property')->findOrFail($id);

        try {
            $listing = $approved
                ? $this->listings->approve($listing, $request->user())
                : $this->listings->reject($listing, $request->user());
        } catch (ListingActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($listing);
    }
}
