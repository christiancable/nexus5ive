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

    public $authorUrl;

    public $authorName;

    public $authorPopname;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($post, $userCanSeeSecrets = false, $readProgress = null)
    {
        $this->id = $post->id;
        $this->title = $post->title ?? null;

        $this->authorUrl = action('App\Http\Controllers\Nexus\UserController@show', ['user' => $post->author->username]);
        $this->authorName = $post->author->username;
        $this->authorPopname = $post->author->popname;

        if ($post->topic->secret && $userCanSeeSecrets == false) {
            $this->authorPopname = null;
            $this->authorUrl = null;
            $this->authorName = null;
        }

        $this->timeClass = 'text-muted';
        if ($readProgress < $post->time) {
            $this->timeClass = 'text-info';
        }

        $this->formattedTime = date('D, F jS Y - H:i', strtotime($post->time));
        if ($post->topic->secret && $userCanSeeSecrets == false) {
            // if we are anonymous them we want to see fuzzy times
            $this->formattedTime = $post->time->diffForHumans();
        }

        $this->content = \App\Helpers\NxCodeHelper::nxDecode($post->text);

        $this->editedByInfo = null;
        if ($post->editor) {
            // if we are anonymous them we want to see fuzzy times
            if ($post->topic->secret && $userCanSeeSecrets == false) {
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
