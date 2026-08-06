<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyMediaController extends Controller
{
    /**
     * List media for a published property. Public.
     */
    public function index($propertyId)
    {
        $property = Property::where('status', 'published')->findOrFail($propertyId);

        $media = $property->media()->orderBy('sort_order')->get();

        return response()->json($media);
    }

    /**
     * Upload a media file for a property owned by the authenticated user.
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);

        if ($property->user_id !== $request->user()->id) {
            abort(403, 'You do not own this property.');
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:20480',
            'caption' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $type = str_starts_with($request->file('file')->getMimeType(), 'video') ? 'video' : 'image';

        $path = $request->file('file')->store("property-media/{$property->id}", 'public');

        $media = $property->media()->create([
            'type' => $type,
            'path' => $path,
            'caption' => $validated['caption'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json($media, 201);
    }

    /**
     * Delete a media file owned by the authenticated user's property.
     */
    public function destroy(Request $request, $propertyId, $mediaId)
    {
        $property = Property::findOrFail($propertyId);

        if ($property->user_id !== $request->user()->id) {
            abort(403, 'You do not own this property.');
        }

        $media = PropertyMedia::where('property_id', $property->id)->findOrFail($mediaId);

        Storage::disk('public')->delete($media->path);
        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }
}
