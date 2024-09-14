<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PostCompose extends Component
{
    public $postText = '';
    public $postTitle = '';
    public $postPreview = 'hello';

    public $topic;
    public $reply;
    public $help;

    public function render()
    {
        return view('livewire.post-compose');
    }

    public function sendPost()
    {
        Log::info('Post message');
    }

    public function updatePreview()
    {
        $this->postPreview = $this->postText;
    }
}
