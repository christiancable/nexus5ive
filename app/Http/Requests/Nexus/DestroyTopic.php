<?php

namespace App\Http\Requests\Nexus;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;

class DestroyTopic extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // get topic from route model binding
        $topic = $this->route('topic');
        return $this->user()->can('delete', $topic);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
