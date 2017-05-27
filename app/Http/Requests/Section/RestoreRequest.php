<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class RestoreRequest extends FormRequest
{
    /**
     * a section can only be restored by
     * [1] the moderator of both the parent and destination sections
     * or
     * [2] the moderator of the section and destination section
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $currentUserID =  \Auth::user()->id;
        $trashedSection = \App\Section::onlyTrashed()->findOrFail($this->section);
        $destinationSection = \App\Section::findOrFail($this->destination);


        if ($destinationSection->moderator->id === $currentUserID) {
            // case [1]
            if ($trashedSection->parent != null) {
                if ($trashedSection->parent->moderator->id === $currentUserID) {
                    $return = true;
                }
            }

            // case [2]
            if ($trashedSection->moderator->id === $currentUserID) {
                $return = true;
            }
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
