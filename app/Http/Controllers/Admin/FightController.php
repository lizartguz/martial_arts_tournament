<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FightController extends Controller
{
    /**
     * Muestra la pantalla administrativa de combates.
     */
    public function index()
    {
        Gate::authorize('fights.view');

        return view('admin.fights.index');
    }
}
