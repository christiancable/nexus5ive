<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class DestroyRequest extends FormRequest
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

        $section = \App\Section::findOrFail($this->section);
        $parent = \App\Section::findOrFail($section->parent_id);

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
