<?php

namespace App\Services;

use App\Exceptions\ConnectionActionException;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ConnectionService
{
    /**
     * Base query for connections aimed at properties owned by the given user.
     */
    public function mine(User $user): Builder
    {
        return Connection::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Property owner accepts a pending connection request.
     */
    public function accept(Connection $connection, User $user): Connection
    {
        return $this->respond($connection, $user, 'accepted');
    }

    /**
     * Property owner rejects a pending connection request.
     */
    public function reject(Connection $connection, User $user): Connection
    {
        return $this->respond($connection, $user, 'rejected');
    }

    private function respond(Connection $connection, User $user, string $newStatus): Connection
    {
        if (! $connection->property || $connection->property->user_id !== $user->id) {
            throw new ConnectionActionException('You do not own the property this connection is for.', 403);
        }

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
