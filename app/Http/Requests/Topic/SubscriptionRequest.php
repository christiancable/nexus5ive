<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;
use App\Section;

class SubscriptionRequest extends FormRequest
{
    /**
     * a topic and be subscribed / unsubscribed by anyone
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "command" => 'required',
        ];
    }
}
