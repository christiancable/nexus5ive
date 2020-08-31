<?php

namespace App\Http\Requests;

use App\Section;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSection extends FormRequest
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
        $formName = "section{$id}";
        $this->errorBag = 'sectionUpdate' . $id;

        $rules =  [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.user_id" => 'required|numeric',
            "form.{$formName}.title" => 'required',
            "form.{$formName}.intro" => 'nullable',
            "form.{$formName}.parent_id" => 'numeric|nullable',
            "form.{$formName}.weight" => 'numeric|nullable'
        ];

        
        $section = Section::findOrFail($id);
        if (!$section->is_home) {
            // check if this is a valid parent
            $descendants = $section->allChildSections();
            $descendantsIDs = Arr::flatten($descendants->pluck('id')->toArray());

            $rules["form.{$formName}.parent_id"] = [
                    'required',
                    'numeric',
                    'exists:sections,id',
                    Rule::notIn($descendantsIDs),
                    Rule::notIn([$section->id]),
            ];
        }

        return $rules;
    }


    public function messages()
    {
        $id = $this->request->all()['id'] ?? '';
        $formName = "section{$id}";

        return [
            "form.{$formName}.title.required" => 'Section Title is required'
        ];
    }
}
