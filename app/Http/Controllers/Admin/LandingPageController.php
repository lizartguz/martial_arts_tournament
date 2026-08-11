<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class LandingPageController extends Controller
{
    public function index()
    {
        Gate::authorize('landing.update');

        return view('admin.landing.index');
    }
}
