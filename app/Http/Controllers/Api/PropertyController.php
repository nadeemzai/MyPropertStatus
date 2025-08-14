<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * List properties with filters & pagination.
     */
    public function index(Request $request)
    {
        $query = Property::query();

        // Filtering
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

/*         if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', $request->bathrooms);
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        } */

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Eager load relationships if needed
        //$properties = $query->with('agency')->paginate(10);
         $properties = $query->paginate(10);

        return response()->json($properties);
    }

    /**
     * Show property details.
     */
    public function show($id)
    {
        //$property = Property::with('agency')->findOrFail($id);
        $property = Property::findOrFail($id);

        return response()->json($property);
    }
}
