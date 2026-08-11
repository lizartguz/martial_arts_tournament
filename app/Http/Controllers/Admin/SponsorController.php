<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SponsorController extends Controller
{
    /**
     * Muestra la pantalla administrativa de sponsors.
     */
    public function index()
    {
        Gate::authorize('sponsors.view');

        return view('admin.sponsors.index');
    }
}
