<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateRequest extends FormRequest
{
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
        $formName = "sectionCreate";
        return [
            "form.sectionCreate.parent_id" => 'required|numeric',
            "form.sectionCreate.user_id" => 'required|numeric',
            "form.sectionCreate.title" => 'required',
        ];
    }
}
