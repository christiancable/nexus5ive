<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopicHeading extends Component
{
    public string $icon;

    public string $textClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $link,
        public array $status,
    ) {
        // set the icon and colour based on the topic status
        $this->textClass = 'text-primary';
        $this->icon = 'default';

        if ($status['unsubscribed'] ?? false) {
            $this->icon = 'unsubscribed';
            $this->textClass = 'text-muted';
        } elseif ($status['new_posts'] ?? false) {
            $this->icon = 'new_posts';
            $this->textClass = 'text-danger';
        } elseif ($status['never_read'] ?? false) {
            $this->icon = 'never_read';
            $this->textClass = 'text-success';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.topic-heading');
    }
}
