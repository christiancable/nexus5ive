<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateRequest extends FormRequest
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
            'text' => 'required',
        ];
    }
}
