<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class WeightClassController extends Controller
{
    /**
     * Muestra la gestion administrativa de categorias de peso.
     */
    public function index()
    {
        return view('admin.weight-classes.index');
    }
}
