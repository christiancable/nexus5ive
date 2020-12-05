<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTopic extends FormRequest
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
        $formName = "topicUpdate{$id}";
        $this->errorBag = 'topicUpdate' . $id;

        return [
            $formName . ".id"          => 'required|numeric',
            $formName . ".id"          => 'exists:topics,id',
            $formName . ".title"       => 'required',
            $formName . ".intro"       => 'required',
            $formName . ".section_id"  => 'required|numeric',
            $formName . ".section_id"  => 'exists:sections,id',
            $formName . ".weight"      => 'required|numeric',
        ];
    }

    public function messages()
    {
        $id = $this->request->all()['id'] ?? '';
        $formName = "topicUpdate{$id}";

        return [
            $formName . ".title.required" => 'Title is required. Think of this as the subject to be discussed',
            $formName . ".intro.required" => 'Introduction is required. Give a brief introduction to your topic'
        ];
    }
}
