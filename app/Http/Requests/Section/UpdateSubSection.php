<?php

namespace Nexus\Http\Requests\Section;

use Nexus\Http\Requests\Request;

class UpdateSubSection extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * all must be true!
     *
     * [1] a subsection can be edited by the moderator of its parent section
     * [2] a subsection cannot be moved into a decedent section
     * [3] a subsection can only be moved into a section the parent section moderator moderates
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $formName = "subsection{$this::input('id')}";
        $formValues = $this::input('form')[$formName];
      
        $this->session()->flash('subSectionForm', $this::input('id'));

        if (!\Auth::check()) {
            $return = false;
        }

        $subSection = \Nexus\Section::findOrFail($this::input('id'));
        $destination = \Nexus\Section::findOrFail($formValues['parent_id']);
        
        // [1] a subsection can be edited by the moderator of its parent section
        if ($subSection->parent->moderator->id == \Auth::user()->id) {
            $updated_by_parent_moderator = true;
        } else {
            $updated_by_parent_moderator = false;
        }

        // [2] a subsection cannot be moved into a decedent section
        $decedents = \Nexus\Helpers\SectionHelper::allChildSections($subSection);
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
        return [
            'form.subsection'.$this::input('id').'.title' => 'required',
            'form.subsection'.$this::input('id').'.user_id' => 'required|numeric',
        ];
    }
}
