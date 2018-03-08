<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
    *
    * @var string
    */
    protected $errorBag = 'sectionCreate';

    /**
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
            "parent_id" => 'required|numeric',
            "parent_id" => 'exists:sections,id',
            "title" => 'required',
        ];
    }
}
