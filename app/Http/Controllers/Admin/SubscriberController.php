<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SubscriberController extends Controller
{
    public function index()
    {
        Gate::authorize('subscribers.view');

        return view('admin.subscribers.index');
    }
}
