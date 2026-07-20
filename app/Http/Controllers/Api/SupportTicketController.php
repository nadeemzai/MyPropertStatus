<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * List the authenticated user's support tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::where('user_id', $request->user()->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10);

        return response()->json($tickets);
    }

    /**
     * Show one of the authenticated user's support tickets.
     */
    public function show(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        if ($ticket->user_id !== $request->user()->id) {
            abort(403, 'You do not own this support ticket.');
        }

        return response()->json($ticket);
    }

    /**
     * Create a new support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = $request->user()->supportTickets()->create($validated);

        return response()->json($ticket, 201);
    }
}
