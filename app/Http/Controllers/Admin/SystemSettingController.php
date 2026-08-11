<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SystemSettingController extends Controller
{
    public function index()
    {
        Gate::authorize('system_settings.view');

        return view('admin.system-settings.index');
    }
}
