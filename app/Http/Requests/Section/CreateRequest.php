<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateRequest extends FormRequest
{
    /**
     * a user can create a section if they
     * moderate the current section
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $formName = "sectionCreate";
        $formValues = $this::input('form')[$formName];
        $this->session()->flash('form', $formName);

        $parentSection = \App\Section::findOrFail($formValues['parent_id']);
        if (\Auth::user()->id == $parentSection->moderator->id) {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $formName = "sectionCreate";
        return [
            "form.{$formName}.parent_id" => 'required|numeric',
            "form.{$formName}.user_id" => 'required|numeric',
            "form.{$formName}.title" => 'required',
        ];
    }
}
