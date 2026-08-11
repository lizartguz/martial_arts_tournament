<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);

        return view('admin.logs.index');
    }

    public function download(Request $request)
    {
        abort_unless($request->user()?->can('logs.download'), 403);

        $path = storage_path('logs/laravel.log');

        abort_unless(file_exists($path), 404);

        return response()->download($path, 'laravel.log');
    }
}
