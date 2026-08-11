<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class UserSubscriptionController extends Controller
{
    /**
     * Muestra la pantalla administrativa de suscripciones de usuario.
     */
    public function index()
    {
        Gate::authorize('user_subscriptions.view');

        return view('admin.user-subscriptions.index');
    }
}
