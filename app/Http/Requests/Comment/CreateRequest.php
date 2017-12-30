<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateRequest extends FormRequest
{
    /**
     * Only logged in users can leave comments on profiles
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
