<?php

namespace App\Http\Requests\Section;

use App\Section;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     * parent section must be a valid section
     * @return array
     */
    public function rules()
    {
        $formName = key($this::input('form'));
        $id = $this::input("form.{$formName}.id");
        $section = Section::findOrFail($id);

        // it not valid to move a section into a descendant
        $descendants = \App\Helpers\SectionHelper::allChildSections($section);
        $descendantsIDs = array_flatten($descendants->pluck('id')->toArray());
        
        // if parents exists then it much be a valid section id
        $allSectionIDs = \App\Section::all('id')->pluck('id')->toArray();
        
        return [
            "form.{$formName}.parent_id" => [
                'numeric',
                Rule::notIn($descendantsIDs),
                Rule::notIn([$id]),
                Rule::In($allSectionIDs),
            ],

            "form.{$formName}.title" => 'required',
            "form.{$formName}.user_id" => 'required|numeric',
        ];
    }
}
