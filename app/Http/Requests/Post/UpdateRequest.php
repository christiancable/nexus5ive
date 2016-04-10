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
     *
     * @todo: a post can be edited by the creator of the post within X seconds
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

        // is the auth user a moderator of the current section
        $post = \Nexus\Post::findOrFail($this::input('id'));

        try {
            if ($post->topic->section->moderator->id == \Auth::id()) {
                $return = true;
            }
        } catch (\Exception $e) {
            $return = false;
            \Log::error('Post Update - attempt edit edit post by non-moderator '. $e);
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
