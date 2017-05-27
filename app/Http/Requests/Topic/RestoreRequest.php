<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class RestoreRequest extends FormRequest
{
    /**
     * a topic can only be restored by
     * the moderator of the section and destination section
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        $currentUserID =  \Auth::user()->id;
        $trashedTopic = \App\Topic::onlyTrashed()->findOrFail($this->topic);
        $originalSection = \App\Section::withTrashed()->findOrFail($trashedTopic->section_id);
        $destinationSection = \App\Section::findOrFail($this->destination);

        if (($destinationSection->moderator->id === $currentUserID) &&
            ($originalSection->moderator->id === $currentUserID)) {
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
