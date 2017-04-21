<?php

namespace Nexus\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use Nexus\Http\Requests\Request;
use Nexus\Section;
use Nexus\Topic;
use Log;

class DestroyRequest extends FormRequest
{
    /**
     * user can delete the topic if they are the moderator or an administrator
     * @return bool
     */
    public function authorize()
    {
        $return = false;
        $topic = \Nexus\Topic::findOrFail($this->topic);

        if (\Auth::check()) {
            $authUser = \Auth::user();
               
            // is the user an administrator
            if ($authUser->administrator) {
                $return = true;
            }

            // or the user is the section moderator
            if ($authUser->id === $topic->section->moderator->id) {
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
            //
        ];
    }
}
