<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Helpers\NxCodeHelper;
use App\Helpers\BoilerplateHelper;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\MentionHelper;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Nexus\TopicController;

class PostCompose extends Component
{
    public $title = '';

    #[Validate('required')]
    public $text = '';

    public $postPreview = '';
    public $previewActive = false;
    public $composeActive = true;
    public $topic;
    public $reply;
    public $help;

    public function mount()
    {
        $help = BoilerplateHelper::formattingHelp();
    }

    public function render()
    {
        return view('livewire.post-compose');
    }

    public function save(Request $request)
    {
        if ($request->user()->cannot('create', [Post::class, $this->topic])) {
            abort(403);
        }
        $this->validate();

        // create the post
        $post = new Post($this->only('title', 'text'));
        $post->topic_id = $this->topic->id;
        $post->user_id = $request->user()->id;
        $post->popname = $request->user()->popname;
        $post->time = Carbon::now();
        $post->save();

        $request->user()->incrementTotalPosts();

        // scan post for mentions
        MentionHelper::makeMentions($post);



        $redirect = action([TopicController::class, 'show'], ['topic' => $post->topic_id]);

        if ($request->user()->viewLatestPostFirst) {
            // @todo this does not redirect as expected
            $redirect .= '#' . $post->id;
        }    
        return redirect()->to($redirect);
    }

    public function showCompose()
    {
        $this->previewActive = false;
        $this->composeActive = true;
    }

    public function showPreview()
    {
        $this->postPreview = NxCodeHelper::nxDecode($this->text);
        $this->previewActive = true;
        $this->composeActive = false;
    }
}
