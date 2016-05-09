<?php

namespace Nexus\Http\Requests\Section;

use Nexus\Http\Requests\Request;

class CreateRequest extends Request
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

        $parentSection = \Nexus\Section::findOrFail($formValues['parent_id']);
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
