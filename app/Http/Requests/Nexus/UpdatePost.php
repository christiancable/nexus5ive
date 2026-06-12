<?php

namespace App\Http\Requests\Nexus;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->request->all()['id'] ?? '';
        $this->errorBag = 'postUpdate'.$id;

        return [
            "form.$id.text" => 'required',
            "form.$id.title" => 'nullable',
        ];
    }

    public function messages(): array
    {
        $id = $this->request->all()['id'] ?? '';

        return [
            "form.$id.text.required" => 'Posts cannot be empty',
        ];
    }
}
