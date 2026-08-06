<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PropertyService
{
    /**
     * Base query for publicly browsable properties.
     */
    public function published(): Builder
    {
        return Property::query()
            ->where('status', 'published')
            ->with(['listings.agency', 'media']);
    }

    /**
     * Base query for a user's own properties, any status. Callers should
     * eager-load whichever relations their view actually needs.
     */
    public function mine(User $user): Builder
    {
        return Property::query()->where('user_id', $user->id);
    }

    /**
     * Scope a "mine" query to the Active or Archived dashboard tab.
     */
    public function byTab(Builder $query, string $tab): Builder
    {
        return $tab === 'archived'
            ? $query->where('status', 'archived')
            : $query->where('status', '!=', 'archived');
    }

    /**
     * Apply the standard browse filters (location, price range, type, agency) to a query.
     */
    public function filter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['agency_id'])) {
            $query->whereHas('listings', function ($q) use ($filters) {
                $q->where('agency_id', $filters['agency_id']);
            });
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query;
    }
}
