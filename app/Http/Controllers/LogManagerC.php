<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogManagerC extends Controller
{
    /**
     * Muestra la pantalla interna de gestion de logs.
     */
    public function index()
    {
        return view('log-manager.index');
    }

    /**
     * Descarga el archivo laravel.log para usuarios autorizados.
     */
    public function download(Request $request)
    {
        if (! $request->user()?->can('administration.logs.view')) {
            abort(403);
        }

        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path, 'laravel.log');
    }
}
