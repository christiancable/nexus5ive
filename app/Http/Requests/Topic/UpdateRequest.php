<?php

namespace Nexus\Http\Requests\Topic;

use Nexus\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * a topic can be updated by the section moderator or by an administrator
     *
     * a topic can be moved to another section if the user moderators that section or
     * they are an administrator
     *
     * @return bool
     */
    public function authorize()
    {
        $return = false;

        if (!\Auth::check()) {
            $return = false;
        }

        $formName = key($this::input('form'));
        $formValues = $this::input('form')[$formName];
        $this->session()->flash('form', $formName);

        // does the user moderate the section that this topic is currently in?
        $topic = \Nexus\Topic::findOrFail($formValues['id']);
        if ($topic->section->moderator->id == \Auth::id()) {
            $return = true;
        }
        
        // is the user moving the topic to a section they moderate?
        try {
            \Auth::user()->sections()->where('id', $formValues['section_id'])->firstOrFail();
        } catch (\Exception $e) {
            $return = false;
            \Log::error('Topic Update - Attempt to move to unowned section '. $e);
        }

        // if the user is an admin then we assume they can do all
        if (\Auth::user()->administrator) {
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

        $formName = key($this::input('form'));
        
        return [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.intro" => 'required',
            "form.{$formName}.section_id" => 'required|numeric',
            "form.{$formName}.weight" => 'required|numeric',
        ];
    }
}
