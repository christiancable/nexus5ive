<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopic extends FormRequest
{
    /**
    * The key to be used for the view error bag.
    *
    * @var string
    */
    protected $errorBag = 'topicCreate';

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
            "title"         => 'required',
            "intro"         => 'required',
            "section_id"    => 'required|numeric|exists:sections,id',
            "weight"        => 'required|numeric',
            "secret"        => 'required|numeric',
            "readonly"      => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            "title.required" => 'Title is required. Think of this as the subject to be discussed',
            "intro.required" => 'Introduction is required. Give a brief introduction to your topic'
        ];
    }
}
