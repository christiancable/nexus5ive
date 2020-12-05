<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePost extends FormRequest
{
    /**
     * The key to be used for the view error bag.
    *
    * @var string
    */
    protected $errorBag = 'postStore';

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
                'text'     => 'required',
                'topic_id' => 'required|exists:topics,id',
                'title'    => 'nullable'
            ];
    }

    public function messages()
    {
        return [
            "text.required" => 'Text is required. You cannot leave empty posts'
        ];
    }
}
