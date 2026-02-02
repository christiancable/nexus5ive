<?php

namespace App\Http\Requests\Nexus;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTopic extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Topic::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->request->all()['id'] ?? '';
        $formName = "topicUpdate{$id}";
        $this->errorBag = 'topicUpdate'.$id;

        return [
            $formName.'.id' => 'required|numeric|exists:topics,id',
            $formName.'.title' => 'required',
            $formName.'.intro' => 'required',
            $formName.'.section_id' => 'required|numeric|exists:sections,id',
            $formName.'.weight' => 'required|numeric',
            $formName.'.readonly' => 'required|boolean',
            $formName.'.secret' => 'required|boolean',
            $formName.'.sticky' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        $id = $this->request->all()['id'] ?? '';
        $formName = "topicUpdate{$id}";

        return [
            $formName.'.title.required' => 'Title is required. Think of this as the subject to be discussed',
            $formName.'.intro.required' => 'Introduction is required. Give a brief introduction to your topic',
        ];
    }
}
