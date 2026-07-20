<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    /**
     * Agency lists the connections it has initiated.
     */
    public function indexForAgency(Request $request)
    {
        $agency = $request->user();

        $query = Connection::where('agency_id', $agency->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $connections = $query->with('property')->paginate(10);

        return response()->json($connections);
    }

    /**
     * Agency initiates a connection to a property owner, or to a phone
     * number that isn't a registered user yet.
     */
    public function store(Request $request)
    {
        $agency = $request->user();

        $validated = $request->validate([
            'property_id' => 'required_without:target_phone|nullable|integer|exists:properties,id',
            'target_phone' => 'required_without:property_id|nullable|string|max:255',
            'message' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $connection = Connection::create([
            'agency_id' => $agency->id,
            'property_id' => $validated['property_id'] ?? null,
            'target_phone' => $validated['target_phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($connection, 201);
    }

    /**
     * Property owner lists connection requests aimed at their properties.
     */
    public function index(Request $request)
    {
        $query = Connection::whereHas('property', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $connections = $query->with(['property', 'agency'])->paginate(10);

        return response()->json($connections);
    }

    public function accept(Request $request, $id)
    {
        return $this->respond($request, $id, 'accepted');
    }

    public function reject(Request $request, $id)
    {
        return $this->respond($request, $id, 'rejected');
    }

    private function respond(Request $request, $id, string $newStatus)
    {
        $connection = Connection::with('property')->findOrFail($id);

        if (! $connection->property || $connection->property->user_id !== $request->user()->id) {
            abort(403, 'You do not own the property this connection is for.');
        }

        if ($connection->expires_at && $connection->expires_at->isPast() && $connection->status === 'pending') {
            $connection->update(['status' => 'expired']);
        }

        if ($connection->status !== 'pending') {
            return response()->json([
                'message' => "This connection request is no longer pending (status: {$connection->status}).",
            ], 409);
        }

        $connection->update(['status' => $newStatus]);

        return response()->json($connection);
    }
}
