<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConnectionActionException;
use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\Notification;
use App\Models\Property;
use App\Services\ConnectionService;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function __construct(private ConnectionService $connections) {}

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

        if ($connection->property_id) {
            $property = Property::find($connection->property_id);

            if ($property) {
                Notification::create([
                    'user_id' => $property->user_id,
                    'title' => 'New connection request',
                    'message' => "{$agency->name} wants to connect about {$property->title}.",
                    'payload' => ['type' => 'connection_request', 'connection_id' => $connection->id],
                ]);
            }
        }

        return response()->json($connection, 201);
    }

    /**
     * Property owner lists connection requests aimed at their properties.
     */
    public function index(Request $request)
    {
        $query = $this->connections->mine($request->user());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $connections = $query->with(['property', 'agency'])->paginate(10);

        return response()->json($connections);
    }

    public function accept(Request $request, $id)
    {
        return $this->respond($request, $id, true);
    }

    public function reject(Request $request, $id)
    {
        return $this->respond($request, $id, false);
    }

    private function respond(Request $request, $id, bool $accepted)
    {
        $connection = Connection::with('property')->findOrFail($id);

        try {
            $connection = $accepted
                ? $this->connections->accept($connection, $request->user())
                : $this->connections->reject($connection, $request->user());
        } catch (ConnectionActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($connection);
    }
}
