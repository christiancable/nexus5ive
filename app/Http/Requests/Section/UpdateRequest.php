<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * For the Current Section
     *      user must be the moderator
     *
     * For Subsections all these must be true!
     * [1] a subsection can be edited by the moderator of its parent section
     * [2] a subsection cannot be moved into a decedent section
     * [3] a subsection can only be moved into a section the parent section moderator moderates
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $formName = key($this::input('form'));
        $formValues = $this::input('form')[$formName];
      
        // dd($formValues, $formName);
        $this->session()->flash('form', $formName);

        if (!\Auth::check()) {
            $return = false;
        }

        // if $formValues['parent_id'] is null then we are editing the main menu
        $section = \App\Section::findOrFail($formValues['id']);
        
        // are we editing the current section OR a sub section
        if ($formValues['id'] === $formValues['current_section']) {
            // current section
            if ($section->moderator->id == \Auth::user()->id) {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            // sub section
            $destination = \App\Section::findOrFail($formValues['parent_id']);

            // [1] a subsection can be edited by the moderator of its parent section
            if ($section->parent->moderator->id == \Auth::user()->id) {
                $updated_by_parent_moderator = true;
            } else {
                $updated_by_parent_moderator = false;
            }

            // [2] a subsection cannot be moved into a decedent section
            $decedents = \App\Helpers\SectionHelper::allChildSections($section);
            if ($decedents->where('id', $destination->id)->count() > 0) {
                $destination_not_child = false;
            } else {
                $destination_not_child = true;
            }

            // [3] a subsection can only be moved into a section the moderator moderates
            if ($destination->moderator->id == \Auth::user()->id) {
                $destination_moderated_by_editor = true;
            } else {
                $destination_moderated_by_editor = false;
            }

            // if all conditions are matched then this is allowed
            if ($updated_by_parent_moderator && $destination_not_child && $destination_moderated_by_editor) {
                $return = true;
            } else {
                $return = false;
            }
        }


        return $return;
    }

    /**
     * Get the validation rules that apply to the request.
     * - id
     * - parent id
     * - moderator
     *
     * @return array
     */
    public function rules()
    {
        $formName = key($this::input('form'));
        return [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.user_id" => 'required|numeric',
        ];
    }
}
