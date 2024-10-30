<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function formatDate($date): string
    {
        return $date->isoFormat('dddd DD MMMM YYYY [at] HH:mm');
    }

    public function classy($score): string
    {
        if ($score < 10) {
            return 'text-secondary';
        }

        if ($score < 100) {
            return 'text-dark';
        }

        if ($score < 1000) {
            return 'text-info';
        }

        if ($score < 10000) {
            return 'text-primary';
        }

        if ($score < 100000) {
            return 'text-success';
        }

        return 'text-danger';
    }

    public function headingBackground($score): string
    {
        if ($score < 1) {
            return 'bg-light';
        }

        return 'bg-info';
    }

    public function headingForeground($score): string
    {
        if ($score < 1) {
            return 'text-secondary';
        }

        return 'text-white';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-card');
    }
}
