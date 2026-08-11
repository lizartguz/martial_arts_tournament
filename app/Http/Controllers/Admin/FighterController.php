<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FighterController extends Controller
{
    /**
     * Muestra la pantalla administrativa de peleadores.
     */
    public function index()
    {
        Gate::authorize('fighters.view');

        return view('admin.fighters.index');
    }
}
