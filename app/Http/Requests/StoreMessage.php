<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessage extends FormRequest
{
    /**
     * The key to be used for the view error bag.
    *
    * @var string
    */
    protected $errorBag = 'messageStore';

    /**
     * Determine if the user is authorized to make this request.
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
            'user_id' => 'required|numeric|exists:users,id'
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Sending empty messages is a little creepy!'
        ];
    }
}
