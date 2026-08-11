<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class EventMediaController extends Controller
{
    public function index()
    {
        Gate::authorize('event_media.view');

        return view('admin.event-media.index');
    }
}
