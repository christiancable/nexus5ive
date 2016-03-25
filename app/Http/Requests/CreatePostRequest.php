<?php

namespace Nexus\Http\Requests;

use Nexus\Http\Requests\Request;
use Nexus\Topic;
use Nexus\Section;

class CreatePostRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * user should be
     * logged in
     * user sysop | user moderator | topic NOT ready only
     *
     * @return bool
     */
    public function authorize()
    {

        $return = false;
        $topic = Topic::findOrFail($this::input('topic_id'));
        $section =  Section::findOrFail($topic->section_id);

        if (\Auth::check()) {
            $authUser = \Auth::user();

            // is the user a sysop
            if ($authUser->administrator) {
                $return = true;
            }

            // OR is the user the moderator
            if ($authUser->id === $section->moderator->id) {
                $return = true;
            }

            // OR is the topic NOT ready only
            if(!$topic->readonly) {
                $return = true;
            }
        
        } else {
            $return = false;
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

            'text' => 'required',
            
        ];
    }
}
