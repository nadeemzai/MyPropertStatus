<?php
namespace App\Repositories;

use App\Models\Property;
use Spatie\QueryBuilder\QueryBuilder;

class PropertyRepository
{
    public function all()
    {
        return QueryBuilder::for(Property::class)
            ->allowedFilters(['title', 'price'])
            ->allowedSorts(['title', 'price'])
            ->paginate();
    }

    public function create(array $data)
    {
        return Property::create($data);
    }

    public function update(Property $property, array $data)
    {
        $property->update($data);
        return $property;
    }

    public function delete(Property $property)
    {
        return $property->delete();
    }
}
