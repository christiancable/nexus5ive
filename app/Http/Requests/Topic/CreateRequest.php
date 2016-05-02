<?php

namespace Nexus\Http\Requests\Topic;

use Nexus\Http\Requests\Request;
use Nexus\Section;

class CreateRequest extends Request
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $formName = "topicCreate";
        return [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.intro" => 'required',
            "form.{$formName}.section_id" => 'required|numeric',
            "form.{$formName}.weight" => 'required|numeric',
        ];
    }
}
