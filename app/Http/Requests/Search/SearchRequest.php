<?php

namespace Nexus\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;
use Nexus\Http\Requests\Request;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
             'text' => 'required',
        ];
    }
}
