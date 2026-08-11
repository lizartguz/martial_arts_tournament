<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Muestra la gestión administrativa de usuarios y roles asignados.
     */
    public function index()
    {
        Gate::authorize('users.view');

        return view('admin.users.index');
    }
}
