<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class LanguageSelect extends Component
{
    public $selectedLanguage;
    public $triggerClass;
    protected $listeners = ['setLanguage', 'updateSession'];

    public function mount($triggerClass = null)
    {
        $this->triggerClass = $triggerClass ?? '';
        $this->selectedLanguage = request()->cookie('locale');
        if (!$this->selectedLanguage) {
            $this->selectedLanguage = config('app.locale');
        }
    }

    public function setLanguage($value)
    {
        session(['locale' => $value]);
        $this->selectedLanguage = $value;
        Cookie::queue('locale', $value, 525600);
        return redirect(request()->header('Referer'));
    }

    public function updateSession($value)
    {
        session(['locale' => $value]);
        $this->selectedLanguage = $value;
        Cookie::queue('locale', $value, 525600);
        $this->selectedLanguage = $value;
    }

    public function render()
    {
        $languages = [
            'en' => [
                'label' => 'English',
                'icon' => 'images/flags/us.svg',
            ],
            'es' => [
                'label' => 'Español',
                'icon' => 'images/flags/bo.svg',
            ],
            'fr' => [
                'label' => 'Français',
                'icon' => 'images/flags/fr.svg',
            ],
            'de' => [
                'label' => 'Deutsch',
                'icon' => 'images/flags/de.svg',
            ],
            'pt' => [
                'label' => 'Português',
                'icon' => 'images/flags/br.svg',
            ],
            'ru' => [
                'label' => 'Русский',
                'icon' => 'images/flags/ru.svg',
            ],
        ];

        return view('livewire.components.language-select', compact('languages'));
    }
}
