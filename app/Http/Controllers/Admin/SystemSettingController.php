<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SystemSettingController extends Controller
{
    /**
     * Muestra la pantalla administrativa de ajustes del sistema.
     */
    public function index()
    {
        Gate::authorize('system_settings.view');

        return view('admin.system-settings.index');
    }
}
