<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = auth()->user()
            ->tickets()
            ->with(['event', 'ticketType', 'order'])
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['event', 'ticketType', 'order']);

        return view('tickets.show', compact('ticket'));
    }
}
