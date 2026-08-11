<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class EventMediaController extends Controller
{
    /**
     * Muestra la pantalla administrativa de multimedia de eventos.
     */
    public function index()
    {
        Gate::authorize('event_media.view');

        return view('admin.event-media.index');
    }
}
