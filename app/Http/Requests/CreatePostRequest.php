<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

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


        if (\Auth::check()) {
            $authUser = \Auth::user();

            // is the user a sysop
            if ($authUser->Sysop) {
                $return = true;
            }

            // @todo add other things here!
            // OR is the user the moderator
            //
            // OR is the topic NOT ready only

            $return = true;
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

            'message_text' => 'required',
            //
        ];
    }
}
