<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Helpers\NxCodeHelper;

class PostCompose extends Component
{
    public $postText = '';
    public $postTitle = '';
    public $postPreview = 'hello';

    public $previewActive = false; 
    public $composeActive = true;
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

    public function showCompose()
    {
        $this->previewActive = false;
        $this->composeActive = true;
    }

    public function showPreview()
    {
        $this->postPreview = NxCodeHelper::nxDecode($this->postText);
        $this->previewActive = true; 
        $this->composeActive = false;
    }
}
