<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePost extends FormRequest
{
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
        $id = $this->request->all()['id'] ?? '';
        $this->errorBag = 'postUpdate'.$id;

        return [
            "form.$id.text" => 'required',
            "form.$id.title" => 'nullable',
        ];
    }

    public function messages()
    {
        $id = $this->request->all()['id'] ?? '';

        return [
            "form.$id.text.required" => 'Posts cannot be empty',
        ];
    }
}
