<?php

namespace Nexus\Http\Requests\Message;

use Nexus\Http\Requests\Request;

class Create extends Request
{
    /**
     * Only logged in users can leave comments on profiles
     *
     * @return bool
     */
    public function authorize()
    {
        if (\Auth::check()) {
            return true;
        } else {
            return false;
            
        }
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

            //
        ];
    }
}
