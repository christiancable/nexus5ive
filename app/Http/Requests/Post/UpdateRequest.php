<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
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
             'form.'.$this::input('id').'.text' => 'required',
        ];
    }
}
