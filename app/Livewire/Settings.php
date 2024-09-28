<?php

namespace App\Livewire;

use App\Models\Mode;
use App\Models\Theme;
use Livewire\Component;

class Settings extends Component
{
    public $modes;
    public $themes;
    public $currentMode;
    public $selectedMode;
    public $welcomeText;

    public function mount()
    {
        $this->modes = Mode::all()->keyBy('id');
        $this->themes = Theme::all()->keyBy('id');
        $this->currentMode = Mode::where('active', 1)->first() ?? Mode::first();
        $this->welcomeText = $this->currentMode->welcome;
    }

    public function updateCurrentMode()
    {
        $this->welcomeText = $this->modes[$this->selectedMode]['welcome'];
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
