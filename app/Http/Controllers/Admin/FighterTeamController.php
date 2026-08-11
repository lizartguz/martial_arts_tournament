<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class FighterTeamController extends Controller
{
    public function index()
    {
        Gate::authorize('fighter_teams.view');

        return view('admin.fighter-teams.index');
    }
}
