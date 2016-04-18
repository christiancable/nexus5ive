<?php

namespace Nexus\Http\Requests\Post;

use Nexus\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to update a given post
     *
     * a post can be edited at any time by
     * - topic moderator
     * - bbs administrator
     * - the creator of the post within X seconds if that post is the most recent in the topic
     *
     * @return bool
     */

    public function authorize()
    {
        $return = false;

        // specify which form we are dealing with to separate
        // the errors in the form
        // @todo: this seems like a dumb method
        $this->session()->flash('postForm', $this::input('id'));

        if (!\Auth::check()) {
            $return = false;
        }

        $post = \Nexus\Post::findOrFail($this::input('id'));
        
        // is this the most recent post in this topic, is it by the logged in user and is it recent
        $latestPost = $post->topic->posts->last();

        if (($post['id'] == $latestPost['id']) &&
            ($post->author->id == \Auth::user()->id) &&
            ($post->time->diffInSeconds() <= env('NEXUS_RECENT_EDIT') )) {
            $return = true;
        }

        // is the auth user a moderator of the current section
        if ($post->topic->section->moderator->id == \Auth::id()) {
            $return = true;
        }
        
        // is the auth user an administrator of the bbs
        if (\Auth::user()->administrator) {
            $return = true;
        }

        return $return;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'form.'.$this::input('id').'.text' => 'required',
        ];
    }
}
