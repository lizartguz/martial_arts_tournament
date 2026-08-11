<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FightResultController extends Controller
{
    /**
     * Muestra la pantalla administrativa de resultados de combates.
     */
    public function index()
    {
        Gate::authorize('fight_results.view');

        return view('admin.fight-results.index');
    }
}
