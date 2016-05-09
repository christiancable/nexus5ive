<?php

namespace Nexus\Http\Requests\Section;

use Nexus\Http\Requests\Request;

class DestroyRequest extends Request
{
    /**
     * a section can only be archived by the
     * the moderator of the parent section
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $section = \Nexus\Section::findOrFail($this->section);
        $parent = \Nexus\Section::findOrFail($section->parent_id);

        if (\Auth::user()->id === $parent->moderator->id) {
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
        return [
            //
        ];
    }
}
