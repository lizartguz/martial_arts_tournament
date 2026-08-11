<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function index()
    {
        Gate::authorize('events.view');

        return view('admin.events.index');
    }
}
