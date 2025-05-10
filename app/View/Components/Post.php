<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Post extends Component
{
    public $id;

    public $title;

    public $timeClass;

    public $formattedTime;

    public $content;

    public $editedByInfo;

    public $author;

    public $popname;

    public $preview;

    public $show;

    public $anonymous;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($post, $anonymous = false, $readProgress = null, $preview = false)
    {
        $this->id = $post->id ?? null; // posts can be previewed so may not have an id
        $this->title = $post->title ?? null;
        $this->preview = $preview ?? false;
        $this->popname = $post->popname ?? null;
        $this->author = $post->author;
        $this->anonymous = $anonymous;

        $this->timeClass = 'text-muted';
        if ($readProgress < $post->time) {
            $this->timeClass = 'text-info';
        }

        $this->formattedTime = date('D, F jS Y - H:i', strtotime($post->time));

        // if we are anonymous them we want to see fuzzy times
        if ($anonymous) {
            $this->formattedTime = $post->time->diffForHumans();
        }

        $this->content = \App\Helpers\NxCodeHelper::nxDecode($post->text);

        // if this post has been edited then show details of who edited it
        $this->editedByInfo = null;

        if ($post->editor) {
            if ($this->anonymous) {
                $this->editedByInfo = "Edited by <strong>Anonymous</strong> around {$post->updated_at->diffForHumans()}";
            } else {
                $this->editedByInfo = "Edited by <strong>{$post->editor->username}</strong> at {$post->updated_at->format('D, F jS Y - H:i')}";
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.post');
    }
}
