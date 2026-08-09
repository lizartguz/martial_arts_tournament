<?php

namespace App\Http\Controllers;

class CatalogC extends Controller
{
    public function index()
    {
        return view('catalog.index');
    }
}
