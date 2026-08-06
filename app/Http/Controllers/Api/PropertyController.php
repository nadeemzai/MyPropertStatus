<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct(private PropertyService $properties) {}

    /**
     * List published properties with filters & pagination. Public.
     */
    public function index(Request $request)
    {
        $query = $this->properties->filter(
            $this->properties->published(),
            $request->only(['location', 'min_price', 'max_price', 'agency_id', 'type'])
        );

        return response()->json($query->paginate(10));
    }

    /**
     * Show a published property's details. Public.
     */
    public function show($id)
    {
        $property = $this->properties->published()->findOrFail($id);

        return response()->json($property);
    }

    /**
     * List the authenticated user's own properties, any status.
     */
    public function mine(Request $request)
    {
        $properties = $request->user()->properties()
            ->with(['listings.agency', 'media'])
            ->latest()
            ->paginate(10);

        return response()->json($properties);
    }

    /**
     * Create a new property owned by the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'details' => 'nullable|array',
        ]);

        $property = $request->user()->properties()->create($validated);

        return response()->json($property, 201);
    }

    /**
     * Update a property owned by the authenticated user.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->user_id !== $request->user()->id) {
            abort(403, 'You do not own this property.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,published,archived',
            'location' => 'nullable|string|max:255',
            'details' => 'nullable|array',
        ]);

        $property->update($validated);

        return response()->json($property);
    }

    /**
     * Delete (soft-delete) a property owned by the authenticated user.
     */
    public function destroy(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->user_id !== $request->user()->id) {
            abort(403, 'You do not own this property.');
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }
}
