<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id);

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $notifications = $query->latest()->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Mark one of the authenticated user's notifications as read.
     */
    public function markRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'You do not own this notification.');
        }

        $notification->update(['is_read' => true]);

        return response()->json($notification);
    }
}
