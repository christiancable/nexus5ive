<?php

namespace Nexus\Http\Requests\Post;

use Nexus\Http\Requests\Request;

class DeleteRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * true if
     *     user is the moderator of the topic
     *     user is an administrator
     *
     * @todo 
     *     user is the author
     *     post time is within XX sections
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;         

        $post = \Nexus\Post::findOrFail($this->post);

        try {
            if ($post->topic->section->moderator->id == \Auth::id()) {
                $return = true;
            }
        } catch (\Exception $e) {
            $return = false;
            \Log::error('Post Delete - attempt to delete post by non-moderator '. $e);
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
            //
        ];
    }
}
