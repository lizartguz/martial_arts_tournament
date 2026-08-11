<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class TicketLinkController extends Controller
{
    /**
     * Muestra la pantalla administrativa de enlaces de tickets.
     */
    public function index()
    {
        Gate::authorize('ticket_links.view');

        return view('admin.ticket-links.index');
    }
}
