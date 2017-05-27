<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class DeleteRequest extends FormRequest
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

        $post = \App\Post::findOrFail($this->post);

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
