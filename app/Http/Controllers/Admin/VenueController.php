<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class VenueController extends Controller
{
    /**
     * Muestra la gestión administrativa de sedes.
     */
    public function index()
    {
        Gate::authorize('venues.view');

        return view('admin.venues.index');
    }
}
