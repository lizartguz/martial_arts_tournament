<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RankingController extends Controller
{
    public function index()
    {
        Gate::authorize('rankings.view');

        return view('admin.rankings.index');
    }
}
