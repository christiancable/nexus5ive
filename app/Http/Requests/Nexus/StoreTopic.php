<?php

namespace App\Http\Requests\Nexus;

use App\Models\Section;
use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;

class StoreTopic extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'topicCreate';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $section = Section::findOrFail($this->input('section_id'));

        return $section && $this->user()->can('create', [Topic::class, $section]);

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
            'intro' => 'required',
            'section_id' => 'required|numeric|exists:sections,id',
            'weight' => 'nullable|numeric',
            'secret' => 'nullable|numeric',
            'readonly' => 'nullable|numeric',
        ];
    }

    /**
     * Get the validated data from the request.
     * Forces default values for non-moderators.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // If returning a specific key, return as-is
        if ($key !== null) {
            return $validated;
        }

        // Force default values for non-moderators
        $section = Section::findOrFail($this->input('section_id'));
        if ($this->user()->id !== $section->moderator->id && ! $this->user()->administrator) {
            $validated['secret'] = 0;
            $validated['readonly'] = 0;
            $validated['weight'] = 0;
        }

        return $validated;
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required. Think of this as the subject to be discussed',
            'intro.required' => 'Introduction is required. Give a brief introduction to your topic',
        ];
    }
}
