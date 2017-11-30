<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

/**
* shared logic for topic requests
**/
class TopicRequest extends FormRequest
{
    /**
    * shared validation messages for topic requests
    * @return array the validation messages to be return to the user
    **/
    public function messages()
    {
        return [
            "form.*.title.required" => 'Title is required. Think of this as the subject to be discussed',
            "form.*.intro.required" => 'Introduction is required. Give a brief introduction to your topic'
        ];
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $formName = key($this::input('form'));
    
        return [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.intro" => 'required',
            "form.{$formName}.section_id" => 'required|numeric',
            "form.{$formName}.weight" => 'required|numeric',
        ];
    }
}
