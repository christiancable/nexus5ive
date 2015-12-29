<?php

namespace Nexus\Http\Requests\Topic;

use Nexus\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        if (!\Auth::check()) {
            $return = false;
        }

        $topic = \Nexus\Topic::findOrFail($this::input('id'));
        if ($topic->section->moderator->id == \Auth::id()) {
            $return = true;
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
            'title' => 'required',
        ];
    }
}
