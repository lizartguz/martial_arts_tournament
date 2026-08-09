<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Slider extends Component
{
    public $images = [];

    public function render()
    {
        return view('livewire.components.slider');
    }
}
