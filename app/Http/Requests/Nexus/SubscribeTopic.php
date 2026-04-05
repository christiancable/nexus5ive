<?php

namespace App\Http\Requests\Nexus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeTopic extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ! $this->user()->isGuest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'command' => [
                'required',
                Rule::In(['unsubscribe', 'subscribe']),
            ],
        ];
    }
}
