<?php

namespace App\Http\Requests\Nexus;

use Illuminate\Foundation\Http\FormRequest;

class StoreComment extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'commentCreate';

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
            'user_id' => 'required|numeric|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Comment Text required',
            'user_id.required' => 'User ID required',
            'user_id.exists' => 'Unknown user',
        ];
    }
}
