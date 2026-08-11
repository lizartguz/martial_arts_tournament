<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RankingController extends Controller
{
    /**
     * Muestra la pantalla administrativa de rankings.
     */
    public function index()
    {
        Gate::authorize('rankings.view');

        return view('admin.rankings.index');
    }
}
