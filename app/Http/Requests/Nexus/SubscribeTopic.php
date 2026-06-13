<?php

namespace App\Http\Requests\Nexus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeTopic extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! $this->user()->isGuest();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'command' => [
                'required',
                Rule::In(['unsubscribe', 'subscribe']),
            ],
        ];
    }
}
