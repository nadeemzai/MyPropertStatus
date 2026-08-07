<?php

namespace App\Services;

use App\Exceptions\ConnectionActionException;
use App\Models\Agency;
use App\Models\Connection;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ConnectionService
{
    /**
     * Base query for agency-initiated connections aimed at properties owned by the given user
     * — i.e. incoming requests the owner can accept/reject.
     */
    public function mine(User $user): Builder
    {
        return Connection::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('initiated_by', 'agency');
    }

    /**
     * Base query for connections the given owner has sent to agencies — read-only,
     * awaiting the agency's response.
     */
    public function sentByMe(User $user): Builder
    {
        return Connection::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('initiated_by', 'owner');
    }

    /**
     * Property owner initiates a connection request to an agency.
     */
    public function initiate(User $user, Property $property, Agency $agency, ?string $message): Connection
    {
        if ($property->user_id !== $user->id) {
            throw new ConnectionActionException('You do not own this property.', 403);
        }

        return Connection::create([
            'agency_id' => $agency->id,
            'property_id' => $property->id,
            'initiated_by' => 'owner',
            'message' => $message,
            'status' => 'pending',
        ]);
    }

    /**
     * Property owner accepts a pending, agency-initiated connection request.
     */
    public function accept(Connection $connection, User $user): Connection
    {
        if (! $connection->property || $connection->property->user_id !== $user->id) {
            throw new ConnectionActionException('You do not own the property this connection is for.', 403);
        }

        return $this->respond($connection, 'accepted');
    }

    /**
     * Property owner rejects a pending, agency-initiated connection request.
     */
    public function reject(Connection $connection, User $user): Connection
    {
        if (! $connection->property || $connection->property->user_id !== $user->id) {
            throw new ConnectionActionException('You do not own the property this connection is for.', 403);
        }

        return $this->respond($connection, 'rejected');
    }

    /**
     * Agency accepts a pending, owner-initiated connection request.
     */
    public function acceptAsAgency(Connection $connection, Agency $agency): Connection
    {
        return $this->respondAsAgency($connection, $agency, 'accepted');
    }

    /**
     * Agency rejects a pending, owner-initiated connection request.
     */
    public function rejectAsAgency(Connection $connection, Agency $agency): Connection
    {
        return $this->respondAsAgency($connection, $agency, 'rejected');
    }

    private function respondAsAgency(Connection $connection, Agency $agency, string $newStatus): Connection
    {
        if ($connection->agency_id !== $agency->id) {
            throw new ConnectionActionException('You do not own this connection.', 403);
        }

        if ($connection->initiated_by !== 'owner') {
            throw new ConnectionActionException('Only owner-initiated connection requests can be responded to here.', 403);
        }

        return $this->respond($connection, $newStatus);
    }

    private function respond(Connection $connection, string $newStatus): Connection
    {
        if ($connection->expires_at && $connection->expires_at->isPast() && $connection->status === 'pending') {
            $connection->update(['status' => 'expired']);
        }

        if ($connection->status !== 'pending') {
            throw new ConnectionActionException(
                "This connection request is no longer pending (status: {$connection->status}).",
                409
            );
        }

        $connection->update(['status' => $newStatus]);

        return $connection;
    }
}
