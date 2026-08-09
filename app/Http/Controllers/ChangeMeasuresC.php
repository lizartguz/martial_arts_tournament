<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangeMeasuresC extends Controller
{
    public function setMetric() {
        session(['measure' => 'metric']);
        return redirect(request()->header('Referer'));
    }

    public function setImperial() {
        session(['measure' => 'imperial']);
        return redirect(request()->header('Referer'));
    }
}
