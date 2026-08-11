<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FighterMediaController extends Controller
{
    /**
     * Muestra la pantalla administrativa de fighter media.
     */
    public function index()
    {
        Gate::authorize('fighter_media.view');

        return view('admin.fighter-media.index');
    }
}
