<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class WeightClassController extends Controller
{
    /**
     * Muestra la pantalla administrativa de categorías de peso.
     */
    public function index()
    {
        return view('admin.weight-classes.index');
    }
}
