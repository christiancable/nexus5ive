<?php

namespace Nexus\Http\Requests;

use Nexus\Http\Requests\Request;
use Nexus\Section;

class CreateTopicRequest extends Request
{
    /**
     * topic can be created by moderators of the current section or bbs sysops
     *
     * user should be
     * logged in
     * user sysop | user moderator 
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        if (\Auth::check()) {
            $authUser = \Auth::user();
            $section =  Section::findOrFail($this::input('section_id'));
               
            // is the user a sysop
            if ($authUser->Sysop) {
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
        return [
            'title' => 'required',
            'intro' => 'required',
            'section_id' => 'required',
            // 'secret' => 'required',
            // 'readonly' => 'required',
            'weight' => 'required',
         
        ];
    }
}
