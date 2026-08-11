<?php

namespace App\Http\Controllers;

class AuthorizationC extends Controller
{
    /**
     * Muestra la pantalla principal del módulo.
     */
    public function index()
    {
        return view('authorization.index');
    }
}
