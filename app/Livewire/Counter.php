<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Counter extends Component
{
    public $count = 1;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement(): void
    {
        $this->count--;
    }

    public function render(): View
    {
        return view('livewire.counter');
    }
}
