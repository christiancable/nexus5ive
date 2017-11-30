<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;
use App\Section;

class CreateRequest extends TopicRequest
{
    /**
     * topic can be created by moderators of the current section or bbs administrators
     *
     * user should be
     * logged in
     * user administrator | user moderator
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $formName = "topicCreate";
        $formValues = $this::input('form')[$formName];
        $this->session()->flash('form', $formName);


        if (\Auth::check()) {
            $authUser = \Auth::user();
            $section =  Section::findOrFail($formValues['section_id']);

            // is the user an administrator
            if ($authUser->administrator) {
                $return = true;
            }

            // or the user is the section moderator
            if ($authUser->id === $section->moderator->id) {
                $return = true;
            }
        } else {
            $return = false;
        }
        
        return $return;
    }
}
