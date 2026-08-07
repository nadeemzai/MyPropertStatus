<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConnectionActionException;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Connection;
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
            'initiated_by' => 'agency',
            'property_id' => $validated['property_id'] ?? null,
            'target_phone' => $validated['target_phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($connection, 201);
    }

    /**
     * Property owner initiates a connection request to an agency.
     */
    public function initiate(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'agency_id' => 'required|integer|exists:agencies,id',
            'message' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $agency = Agency::findOrFail($validated['agency_id']);

        try {
            $connection = $this->connections->initiate($user, $property, $agency, $validated['message'] ?? null);
        } catch (ConnectionActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($connection, 201);
    }

    /**
     * Agency accepts a pending, owner-initiated connection request.
     */
    public function agencyAccept(Request $request, $id)
    {
        return $this->respondAsAgency($request, $id, true);
    }

    /**
     * Agency rejects a pending, owner-initiated connection request.
     */
    public function agencyReject(Request $request, $id)
    {
        return $this->respondAsAgency($request, $id, false);
    }

    private function respondAsAgency(Request $request, $id, bool $accepted)
    {
        $connection = Connection::with('property')->findOrFail($id);

        try {
            $connection = $accepted
                ? $this->connections->acceptAsAgency($connection, $request->user())
                : $this->connections->rejectAsAgency($connection, $request->user());
        } catch (ConnectionActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        return response()->json($connection);
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
