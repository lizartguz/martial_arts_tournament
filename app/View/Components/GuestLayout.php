<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Renderiza la vista con el estado actual del componente.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
