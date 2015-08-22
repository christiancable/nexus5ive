<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCommentRequest extends Request
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
